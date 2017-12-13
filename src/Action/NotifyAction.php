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

        $model = $request->getModel();

        $this->openPayUBridge->setAuthorizationDataApi(
            $this->api['environment'],
            $this->api['signature_key'],
            $this->api['pos_id']
        );

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $body = file_get_contents('php://input');
            $data = trim($body);

            try {
                $result = $this->openPayUBridge->consumeNotification($data);

                if ($result->getResponse()->order->orderId) {
                    /** @var \OpenPayU_Result $order */
                    $order =  $this->openPayUBridge->retrieve($result->getResponse()->order->orderId);

                    if (OpenPayUBridgeInterface::SUCCESS_API_STATUS === $order->getStatus()) {
                        if (PaymentInterface::STATE_COMPLETED !== $payment->getState()) {
                            $status = $order->getResponse()->orders[0]->status;
                            $model['statusPayU'] = $status;
                            $request->setModel($model);
                        }

                        throw new HttpResponse('SUCCESS');
                    }
                }
            } catch (\OpenPayU_Exception $e) {
                throw new HttpResponse($e->getMessage());
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof Notify &&
            $request->getModel() instanceof \ArrayObject
        ;
    }
}
