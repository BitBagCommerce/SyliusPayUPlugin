<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\CommandHandler;

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use BitBag\SyliusPayUPlugin\Command\StatusPaymentRequest;
use BitBag\SyliusPayUPlugin\Processor\PaymentTransitionProcessorInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProviderInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Webmozart\Assert\Assert;

#[AsMessageHandler]
final readonly class StatusPaymentRequestHandler
{
    public function __construct(
        private PaymentRequestProviderInterface $paymentRequestProvider,
        private StateMachineInterface $stateMachine,
        private OpenPayUBridgeInterface $openPayUBridge,
        private PaymentTransitionProcessorInterface $paymentTransitionProcessor,
    ) {
    }

    public function __invoke(StatusPaymentRequest $statusPaymentRequest): void
    {
        $paymentRequest = $this->paymentRequestProvider->provide($statusPaymentRequest);

        $paymentMethod = $paymentRequest->getMethod();
        $gatewayConfig = $paymentMethod->getGatewayConfig();
        Assert::notNull($gatewayConfig, sprintf(
            'The payment method (code: %s) has not been configured.',
            $paymentMethod->getCode(),
        ));

        $this->openPayUBridge->setAuthorizationData(
            $gatewayConfig->getConfig()['environment'],
            $gatewayConfig->getConfig()['signature_key'],
            $gatewayConfig->getConfig()['pos_id'],
            $gatewayConfig->getConfig()['oauth_client_id'],
            $gatewayConfig->getConfig()['oauth_client_secret'],
        );

        $this->paymentTransitionProcessor->process($paymentRequest);

        $this->stateMachine->apply(
            $paymentRequest,
            PaymentRequestTransitions::GRAPH,
            PaymentRequestTransitions::TRANSITION_COMPLETE,
        );
    }
}
