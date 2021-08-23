<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Action;

use ArrayObject;
use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use OpenPayU_Exception;
use OpenPayU_Result;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Notify;
use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;

final class NotifyAction implements ActionInterface, ApiAwareInterface
{
    use GatewayAwareTrait;

    /** @var OpenPayUBridgeInterface */
    private $openPayUBridge;

    /** @param OpenPayUBridgeInterface $openPayUBridge */
    public function __construct(OpenPayUBridgeInterface $openPayUBridge)
    {
        $this->openPayUBridge = $openPayUBridge;
    }

    /**
     * @throws UnsupportedApiException if the given Api is not supported.
     */
    public function setApi($api): void
    {
        if (false === is_array($api)) {
            throw new UnsupportedApiException('Not supported. Expected to be set as array.');
        }

        $this->openPayUBridge->setAuthorizationData(
            $api['environment'],
            $api['signature_key'],
            $api['pos_id'],
            $api['oauth_client_id'],
            $api['oauth_client_secret']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function execute($request): void
    {
        /** @var $request Notify */
        RequestNotSupportedException::assertSupports($this, $request);
        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();
        Assert::isInstanceOf($payment, PaymentInterface::class);

        $model = $request->getModel();

        if ('POST' === $_SERVER['REQUEST_METHOD']) {
            $body = file_get_contents('php://input');
            $data = trim($body);

            try {
                $result = $this->openPayUBridge->consumeNotification($data);

                if (null !== $result) {
                    /** @var mixed $response */
                    $response = $result->getResponse();
                    if ($response->order->orderId) {
                        /** @var OpenPayU_Result $order */
                        $order = $this->openPayUBridge->retrieve($response->order->orderId);
                        if (OpenPayUBridgeInterface::SUCCESS_API_STATUS === $order->getStatus()) {
                            if (PaymentInterface::STATE_COMPLETED !== $payment->getState()) {
                                $status = $order->getResponse()->orders[0]->status;
                                $model['statusPayU'] = $status;
                                $request->setModel($model);
                            }

                            throw new HttpResponse('SUCCESS');
                        }
                    }
                }
            } catch (OpenPayU_Exception $e) {
                throw new HttpResponse($e->getMessage());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request): bool
    {
        return $request instanceof Notify &&
            $request->getModel() instanceof ArrayObject
        ;
    }
}
