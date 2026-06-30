<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\CommandHandler;

use BitBag\SyliusPayUPlugin\Api\Exception\InvalidSignatureException;
use BitBag\SyliusPayUPlugin\Api\Exception\PayUApiException;
use BitBag\SyliusPayUPlugin\Api\PayUClientFactoryInterface;
use BitBag\SyliusPayUPlugin\Builder\OrderPayloadBuilderInterface;
use BitBag\SyliusPayUPlugin\Command\CapturePayUPaymentRequest;
use BitBag\SyliusPayUPlugin\Notification\PayUSignatureVerifierInterface;
use BitBag\SyliusPayUPlugin\PayUGatewayFactory;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\GatewayConfigInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;
use Sylius\Component\Payment\PaymentTransitions;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'sylius.payment_request.command_bus')]
final readonly class CapturePayUPaymentRequestHandler
{
    public function __construct(
        private PaymentRequestProviderInterface $paymentRequestProvider,
        private StateMachineInterface $stateMachine,
        private EntityManagerInterface $entityManager,
        private PayUClientFactoryInterface $payUClientFactory,
        private OrderPayloadBuilderInterface $orderPayloadBuilder,
        private PayUSignatureVerifierInterface $signatureVerifier,
        private LoggerInterface $logger,
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
    ) {
    }

    public function __invoke(CapturePayUPaymentRequest $command): void
    {
        $paymentRequest = $this->paymentRequestProvider->provide($command);

        if ($paymentRequest->getAction() === PaymentRequestInterface::ACTION_STATUS) {
            $this->processStatusAction($paymentRequest);

            return;
        }

        $payload = $paymentRequest->getPayload() ?? [];
        $responseData = $paymentRequest->getResponseData();

        if (isset($payload['http_request'])) {
            $this->processWebhook($paymentRequest, $payload['http_request']);

            return;
        }

        if (isset($responseData['payUOrderId'])) {
            return;
        }

        $this->createPayUOrder($paymentRequest);
    }

    private function processStatusAction(PaymentRequestInterface $paymentRequest): void
    {
        /** @var PaymentInterface $payment */
        $payment = $paymentRequest->getPayment();

        $capturePaymentRequest = $this->paymentRequestRepository->findOneByActionPaymentAndMethod(
            PaymentRequestInterface::ACTION_CAPTURE,
            $payment,
            $paymentRequest->getMethod(),
        );

        if ($capturePaymentRequest === null) {
            return;
        }

        $payUOrderId = $capturePaymentRequest->getResponseData()['payUOrderId'] ?? null;

        if ($payUOrderId === null) {
            return;
        }

        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentRequest->getMethod()->getGatewayConfig();
        $config = $gatewayConfig->getConfig();

        $client = $this->payUClientFactory->createForGatewayConfig($config);

        try {
            $statusResponse = $client->getOrderStatus((string) $payUOrderId);
        } catch (PayUApiException $exception) {
            $this->logger->error('PayU status check on return failed', [
                'paymentRequestId' => $paymentRequest->getId(),
                'payUOrderId' => $payUOrderId,
                'error' => $exception->getMessage(),
            ]);

            return;
        }

        $status = $statusResponse->status;

        $this->logger->info('PayU status check on return', [
            'paymentRequestId' => $paymentRequest->getId(),
            'capturePaymentRequestId' => $capturePaymentRequest->getId(),
            'payUOrderId' => $payUOrderId,
            'status' => $status,
        ]);

        $this->applyStatusTransitions($capturePaymentRequest, $payment, $status);
    }

    private function createPayUOrder(PaymentRequestInterface $paymentRequest): void
    {
        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentRequest->getMethod()->getGatewayConfig();
        $config = $gatewayConfig->getConfig();

        $client = $this->payUClientFactory->createForGatewayConfig($config);
        $payload = $this->orderPayloadBuilder->build($paymentRequest);

        $this->logger->info('Creating PayU order', [
            'paymentRequestId' => $paymentRequest->getId(),
            'extOrderId' => $payload['extOrderId'],
        ]);

        try {
            $response = $client->createOrder($payload);
        } catch (PayUApiException $exception) {
            $this->logger->error('PayU order creation failed', [
                'paymentRequestId' => $paymentRequest->getId(),
                'error' => $exception->getMessage(),
            ]);

            if ($this->stateMachine->can($paymentRequest, PaymentRequestTransitions::GRAPH, PaymentRequestTransitions::TRANSITION_FAIL)) {
                $this->stateMachine->apply($paymentRequest, PaymentRequestTransitions::GRAPH, PaymentRequestTransitions::TRANSITION_FAIL);
            }

            $this->entityManager->flush();

            throw $exception;
        }

        $paymentRequest->setResponseData([
            'payUOrderId' => $response->orderId,
            'redirectUri' => $response->redirectUri,
            'extOrderId' => $payload['extOrderId'],
        ]);

        if ($this->stateMachine->can($paymentRequest, PaymentRequestTransitions::GRAPH, PaymentRequestTransitions::TRANSITION_PROCESS)) {
            $this->stateMachine->apply($paymentRequest, PaymentRequestTransitions::GRAPH, PaymentRequestTransitions::TRANSITION_PROCESS);
        }

        $this->entityManager->flush();

        $this->logger->info('PayU order created', [
            'paymentRequestId' => $paymentRequest->getId(),
            'payUOrderId' => $response->orderId,
        ]);
    }

    /**
     * @param array<string, mixed> $httpRequest
     */
    private function processWebhook(PaymentRequestInterface $paymentRequest, array $httpRequest): void
    {
        $rawBody = (string) ($httpRequest['content'] ?? '');
        $signatureHeader = $httpRequest['headers']['openpayu-signature'][0]
            ?? $httpRequest['headers']['OpenPayU-Signature'][0]
            ?? null;

        if ($signatureHeader === null) {
            $this->logger->error('PayU webhook missing OpenPayU-Signature header', [
                'paymentRequestId' => $paymentRequest->getId(),
            ]);

            throw new InvalidSignatureException('Missing OpenPayU-Signature header in PayU notification.');
        }

        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentRequest->getMethod()->getGatewayConfig();
        $config = $gatewayConfig->getConfig();

        $this->signatureVerifier->verify($rawBody, (string) $signatureHeader, (string) ($config['signature_key'] ?? ''));

        $webhookData = json_decode($rawBody, true);
        $payUOrderId = $webhookData['order']['orderId'] ?? ($paymentRequest->getResponseData()['payUOrderId'] ?? null);

        if ($payUOrderId === null) {
            $this->logger->warning('PayU webhook without orderId', [
                'paymentRequestId' => $paymentRequest->getId(),
            ]);

            return;
        }

        $client = $this->payUClientFactory->createForGatewayConfig($config);

        try {
            $statusResponse = $client->getOrderStatus((string) $payUOrderId);
        } catch (PayUApiException $exception) {
            $this->logger->error('PayU status fetch failed', [
                'paymentRequestId' => $paymentRequest->getId(),
                'payUOrderId' => $payUOrderId,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        $status = $statusResponse->status;

        $this->logger->info('PayU webhook received', [
            'paymentRequestId' => $paymentRequest->getId(),
            'payUOrderId' => $payUOrderId,
            'status' => $status,
        ]);

        /** @var PaymentInterface $payment */
        $payment = $paymentRequest->getPayment();

        $this->applyStatusTransitions($paymentRequest, $payment, $status);
    }

    private function applyStatusTransitions(PaymentRequestInterface $paymentRequest, PaymentInterface $payment, string $status): void
    {
        if (in_array($status, [PayUGatewayFactory::STATUS_COMPLETED, PayUGatewayFactory::STATUS_WAITING_FOR_CONFIRMATION], true)) {
            if ($this->stateMachine->can($paymentRequest, PaymentRequestTransitions::GRAPH, PaymentRequestTransitions::TRANSITION_COMPLETE)) {
                $this->stateMachine->apply($paymentRequest, PaymentRequestTransitions::GRAPH, PaymentRequestTransitions::TRANSITION_COMPLETE);
            }

            if ($this->stateMachine->can($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_COMPLETE)) {
                $this->stateMachine->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_COMPLETE);
            }

            $this->entityManager->flush();

            return;
        }

        if ($status === PayUGatewayFactory::STATUS_CANCELED) {
            if ($this->stateMachine->can($paymentRequest, PaymentRequestTransitions::GRAPH, PaymentRequestTransitions::TRANSITION_CANCEL)) {
                $this->stateMachine->apply($paymentRequest, PaymentRequestTransitions::GRAPH, PaymentRequestTransitions::TRANSITION_CANCEL);
            }

            if ($this->stateMachine->can($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_CANCEL)) {
                $this->stateMachine->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_CANCEL);
            }

            $this->entityManager->flush();
        }
    }
}
