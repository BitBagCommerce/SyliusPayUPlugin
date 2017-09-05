<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\PayUPlugin\Action;

use BitBag\PayUPlugin\Bridge\OpenPayUBridgeInterface;
use BitBag\PayUPlugin\Exception\PayUException;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Notify;
use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class NotifyAction implements ActionInterface, ApiAwareInterface
{
    use GatewayAwareTrait;

    private $api = [];

    /**
     * @var OpenPayUBridgeInterface
     */
    private $openPayUBridge;

    /**
     * @param OpenPayUBridgeInterface $openPayUBridge
     */
    public function __construct(OpenPayUBridgeInterface $openPayUBridge)
    {
        $this->openPayUBridge = $openPayUBridge;
    }

    /**
     * @return \Payum\Core\GatewayInterface
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * {@inheritDoc}
     */
    public function setApi($api)
    {
        if (!is_array($api)) {

            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($request)
    {
        /** @var $request Notify */
        RequestNotSupportedException::assertSupports($this, $request);
        /** @var PaymentInterface $payment */
        $payment = $request->getFirstModel();
        Assert::isInstanceOf($payment, PaymentInterface::class);

        $this->openPayUBridge->setAuthorizationDataApi(
            $this->api['environment'],
            $this->api['pos_id'],
            $this->api['signature_key']
        );


        try {
            $result = $this->openPayUBridge->consumeNotification($_POST);
            $orderId = $result->getResponse()->order->orderId;

            if (true === (bool)$orderId) {
                $order = $this->openPayUBridge->retrieve($orderId);

                if (OpenPayUBridgeInterface::SUCCESS_API_STATUS === $order->getStatus()) {
                    $payment->setState(PaymentInterface::STATE_COMPLETED);
                }
            }

        } catch (PayUException $e) {

            throw new HttpResponse($e->getMessage());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof Notify &&
            $request->getModel() instanceof \ArrayObject &&
            'POST' === $_SERVER['REQUEST_METHOD']
        ;
    }
}
