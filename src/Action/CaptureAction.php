<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Action;

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use BitBag\SyliusPayUPlugin\Exception\PayUException;
use OpenPayU_Configuration;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Request\Capture;
use Payum\Core\Security\GenericTokenFactoryAwareInterface;
use Payum\Core\Security\GenericTokenFactoryInterface;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Webmozart\Assert\Assert;

final class CaptureAction implements ActionInterface, ApiAwareInterface, GenericTokenFactoryAwareInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /** @var OpenPayUBridgeInterface */
    private $openPayUBridge;

    /** @var GenericTokenFactoryInterface */
    private $tokenFactory;

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

    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        $model = $request->getModel();
        /** @var OrderInterface $orderData */
        $order = $request->getFirstModel()->getOrder();

        /** @var TokenInterface $token */
        $token = $request->getToken();
        $payUdata = $this->prepareOrder($token, $order);

        $result = $this->openPayUBridge->create($payUdata);

        if (null !== $model['orderId']) {
            /** @var mixed $response */
            $response = $this->openPayUBridge->retrieve((string) $model['orderId'])->getResponse();
            Assert::keyExists($response->orders, 0);
            if (OpenPayUBridgeInterface::SUCCESS_API_STATUS === $response->status->statusCode) {
                $model['statusPayU'] = $response->orders[0]->status;
                $request->setModel($model);
            }
            if (OpenPayUBridgeInterface::NEW_API_STATUS !== $response->orders[0]->status) {
                return;
            }
        }

        if (null !== $result) {
            /** @var mixed $response */
            $response = $result->getResponse();
            if ($response && OpenPayUBridgeInterface::SUCCESS_API_STATUS === $response->status->statusCode) {
                $model['orderId'] = $response->orderId;

                $request->setModel($model);

                throw new HttpRedirect($response->redirectUri);
            }
        }

        throw PayUException::newInstance($response->status);
    }

    public function setGenericTokenFactory(GenericTokenFactoryInterface $genericTokenFactory = null): void
    {
        $this->tokenFactory = $genericTokenFactory;
    }

    public function supports($request): bool
    {
        return
            $request instanceof Capture
            && $request->getModel() instanceof ArrayObject;
    }

    private function prepareOrder(TokenInterface $token, OrderInterface $order): array
    {
        $notifyToken = $this->tokenFactory->createNotifyToken($token->getGatewayName(), $token->getDetails());
        $payUdata = [];

        $payUdata['continueUrl'] = $token->getTargetUrl();
        $payUdata['notifyUrl'] = $notifyToken->getTargetUrl();
        $payUdata['customerIp'] = $order->getCustomerIp();
        $payUdata['merchantPosId'] = OpenPayU_Configuration::getMerchantPosId();
        $payUdata['description'] = $order->getNumber();
        $payUdata['currencyCode'] = $order->getCurrencyCode();
        $payUdata['totalAmount'] = $order->getTotal();
        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();

        Assert::isInstanceOf(
            $customer,
            CustomerInterface::class,
            sprintf(
                'Make sure the first model is the %s instance.',
                CustomerInterface::class
            )
        );

        $buyer = [
            'email' => (string) $customer->getEmail(),
            'firstName' => (string) $customer->getFirstName(),
            'lastName' => (string) $customer->getLastName(),
            'language' => $this->getFallbackLocaleCode($order->getLocaleCode()),
        ];
        $payUdata['buyer'] = $buyer;
        $payUdata['products'] = $this->getOrderItems($order);

        return $payUdata;
    }

    private function getOrderItems(OrderInterface $order): array
    {
        $itemsData = [];

        if ($items = $order->getItems()) {
            /** @var OrderItemInterface $item */
            foreach ($items as $key => $item) {
                $itemsData[$key] = [
                    'name' => $item->getProductName(),
                    'unitPrice' => $item->getUnitPrice(),
                    'quantity' => $item->getQuantity(),
                ];
            }
        }

        return $itemsData;
    }

    private function getFallbackLocaleCode(string $localeCode): string
    {
        return explode('_', $localeCode)[0];
    }
}
