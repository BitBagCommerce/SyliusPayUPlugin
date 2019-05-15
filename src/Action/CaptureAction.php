<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
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
        ArrayObject::ensureArrayObject($model);

        /** @var OrderInterface $order */
        $order = $request->getFirstModel()->getOrder();

        $model['customer'] = $order->getCustomer();
        $model['locale'] = $this->getFallbackLocaleCode($order->getLocaleCode());

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

        /** @var TokenInterface $token */
        $token = $request->getToken();
        $order = $this->prepareOrder($token, $model);

        $result = $this->openPayUBridge->create($order);
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

    private function prepareOrder(TokenInterface $token, ArrayObject $model): array
    {
        $notifyToken = $this->tokenFactory->createNotifyToken($token->getGatewayName(), $token->getDetails());

        $order = [];
        $order['continueUrl'] = $token->getTargetUrl();
        $order['notifyUrl'] = $notifyToken->getTargetUrl();
        $order['customerIp'] = $model['customerIp'];
        $order['merchantPosId'] = OpenPayU_Configuration::getMerchantPosId();
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
            'email' => (string) $customer->getEmail(),
            'firstName' => (string) $customer->getFirstName(),
            'lastName' => (string) $customer->getLastName(),
            'language' => $model['locale'],
        ];

        $order['buyer'] = $buyer;
        $order['products'] = $this->resolveProducts($model);

        return $order;
    }

    private function resolveProducts(ArrayObject $model): array
    {
        if (!array_key_exists('products', $model) || 0 === count($model['products'])) {
            return [
                [
                    'name' => $model['description'],
                    'unitPrice' => $model['totalAmount'],
                    'quantity' => 1,
                ],
            ];
        }

        return $model['products'];
    }

    private function getFallbackLocaleCode(string $localeCode): string
    {
        return explode('_', $localeCode)[0];
    }
}
