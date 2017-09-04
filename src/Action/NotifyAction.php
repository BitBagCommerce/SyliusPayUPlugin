<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\PayUPlugin\Action;

use BitBag\PayUPlugin\OpenPayUWrapper;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Notify;
use Sylius\Component\Core\Model\PaymentInterface;
use Webmozart\Assert\Assert;
use Payum\Core\ApiAwareInterface;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class NotifyAction implements ActionInterface, ApiAwareInterface
{
    use GatewayAwareTrait;

    private $api = [];

    /**
     * @param mixed $request
     *
     * @throws \Payum\Core\Exception\RequestNotSupportedException if the action dose not support the request.
     */
    public function execute($request)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            /** @var $request Notify */
            RequestNotSupportedException::assertSupports($this, $request);

            /** @var PaymentInterface $payment */
            $payment = $request->getFirstModel();
            Assert::isInstanceOf($payment, PaymentInterface::class);

            $body = file_get_contents('php://input');
            $data = trim($body);

            \OpenPayU_Configuration::setEnvironment($this->api['environment']);
            \OpenPayU_Configuration::setMerchantPosId($this->api['pos_id']);
            \OpenPayU_Configuration::setSignatureKey($this->api['signature_key']);

            try {

                $result = \OpenPayU_Order::consumeNotification($data);

                if ($result->getResponse()->order->orderId) {

                    $order = \OpenPayU_Order::retrieve($result->getResponse()->order->orderId);

                    if($order->getStatus() === OpenPayUWrapper::SUCCESS_API_STATUS){

                        $payment->setState(PaymentInterface::STATE_COMPLETED);
                    }
                }

            } catch (\OpenPayU_Exception $e) {
                throw new HttpResponse($e->getMessage());
            }
        }
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
    public function supports($request)
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayObject
        ;
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
}
