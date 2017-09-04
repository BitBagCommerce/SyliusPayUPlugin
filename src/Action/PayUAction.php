<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\PayUPlugin\Action;

use BitBag\PayUPlugin\Exception\PayUException;
use BitBag\PayUPlugin\OpenPayUWrapper;
use BitBag\PayUPlugin\OpenPayUWrapperInterface;
use BitBag\PayUPlugin\SetPayU;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Webmozart\Assert\Assert;
use Payum\Core\Payum;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class PayUAction implements ApiAwareInterface, ActionInterface
{
    private $api = [];

    /**
     * @var OpenPayUWrapperInterface
     */
    private $openPayUWrapper;

    /**
     * @var Payum
     */
    private $payum;

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
     * @param OpenPayUWrapperInterface $openPayUWrapper
     * @param Payum $payum
     */
    public function __construct(OpenPayUWrapperInterface $openPayUWrapper, Payum $payum)
    {
        $this->payum = $payum;
        $this->setOpenPayUWrapper($openPayUWrapper);
    }

    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);
        $environment = $this->api['environment'];
        $signature = $this->api['signature_key'];
        $posId = $this->api['pos_id'];

        $openPayU = $this->getOpenPayUWrapper();
        $openPayU->setAuthorizationDataApi($environment, $signature, $posId);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if ($model['orderId'] !== null) {
            /** @var mixed $response */
            $response = $openPayU->retrieve($model['orderId'])->getResponse();
            Assert::keyExists($response->orders, 0);

            if ($response->status->statusCode === OpenPayUWrapper::SUCCESS_API_STATUS) {
                $model['status'] = $response->orders[0]->status;
                $request->setModel($model);
            }

            if ($response->orders[0]->status !== OpenPayUWrapper::NEW_API_STATUS) {
                return;
            }
        }

        /**
         * @var TokenInterface $token
         */
        $token = $request->getToken();
        $order = $this->prepareOrder($token, $model, $posId);
        $response = $openPayU->create($order)->getResponse();

        if ($response && $response->status->statusCode === OpenPayUWrapper::SUCCESS_API_STATUS) {
            $model['orderId'] = $response->orderId;
            $request->setModel($model);

            throw new HttpRedirect($response->redirectUri);
        }

        throw PayUException::newInstance($response->status);
    }

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof SetPayU &&
            $request->getModel() instanceof \ArrayObject
        ;
    }

    /**
     * @return OpenPayUWrapperInterface
     */
    public function getOpenPayUWrapper()
    {
        return $this->openPayUWrapper;
    }

    /**
     * @param OpenPayUWrapperInterface $openPayUWrapper
     */
    public function setOpenPayUWrapper($openPayUWrapper)
    {
        $this->openPayUWrapper = $openPayUWrapper;
    }

    private function prepareOrder(TokenInterface $token, $model, $posId)
    {
        $notifyToken = $this->createNotifyToken($token->getGatewayName(), $token->getDetails());

        $order = [];
        $order['continueUrl'] = $token->getTargetUrl();
        $order['notifyUrl'] = $notifyToken->getTargetUrl();
        $order['customerIp'] = $model['customerIp'];
        $order['merchantPosId'] = $posId;
        $order['description'] = $model['description'];
        $order['currencyCode'] = $model['currencyCode'];
        $order['totalAmount'] = $model['totalAmount'];
        $order['extOrderId'] = $model['extOrderId'];
        /** @var CustomerInterface $customer */
        $customer = $model['customer'];

        Assert::isInstanceOf(
            $customer,
            CustomerInterface::class,
            sprintf(
                'Make sure the first model is the %s instance.',
                CustomerInterface::class
            )
        );

        $buyer = [
            'email' => (string)$customer->getEmail(),
            'firstName' => (string)$customer->getFirstName(),
            'lastName' => (string)$customer->getLastName(),
        ];

        $order['buyer'] = $buyer;
        $order['products'] = $this->resolveProducts($model);

        return $order;
    }

    /**
     * @param $model
     *
     * @return array
     */
    private function resolveProducts($model)
    {
        if (!array_key_exists('products', $model) || count($model['products']) === 0) {
            return [
                [
                    'name' => $model['description'],
                    'unitPrice' => $model['totalAmount'],
                    'quantity' => 1
                ]
            ];
        }

        return $model['products'];
    }

    /**
     * @param string $gatewayName
     * @param object $model
     *
     * @return TokenInterface
     */
    private function createNotifyToken($gatewayName, $model)
    {
        return $this->payum->getTokenFactory()->createNotifyToken(
            $gatewayName,
            $model
        );
    }
}