<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Api\Provider;

use OpenPayU_Configuration;
use Sylius\Bundle\CoreBundle\OrderPay\Provider\UrlProviderInterface;
use Sylius\Bundle\PayumBundle\Provider\PaymentDescriptionProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final readonly class PayUDataProvider implements payUDataProviderInterface
{
    public function __construct(
        private UrlProviderInterface $afterPayUrlProvider,
        private PaymentDescriptionProviderInterface $paymentDescriptionProvider,
    ) {
    }

    public function preparePayUCreateData(PaymentRequestInterface $paymentRequest): array
    {
        /** @var PaymentInterface $payment */
        $payment = $paymentRequest->getPayment();
        /** @var OrderInterface $order */
        $order = $payment->getOrder();
        $continueUrl = $this->afterPayUrlProvider->getUrl($paymentRequest, UrlGeneratorInterface::ABSOLUTE_URL);

        $payUData = [];
        $payUData['continueUrl'] = $continueUrl;
        $payUData['customerIp'] = $order->getCustomerIp();
        $payUData['merchantPosId'] = OpenPayU_Configuration::getMerchantPosId();
        $payUData['description'] = $this->paymentDescriptionProvider->getPaymentDescription($payment);
        $payUData['currencyCode'] = $order->getCurrencyCode();
        $payUData['totalAmount'] = $order->getTotal();
        $payUData['tokenValue'] = $order->getTokenValue();
        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();

        $buyer = [
            'email' => (string) $customer->getEmail(),
            'firstName' => (string) $customer->getFirstName(),
            'lastName' => (string) $customer->getLastName(),
            'language' => $this->getFallbackLocaleCode($order->getLocaleCode()),
        ];
        $payUData['buyer'] = $buyer;
        $payUData['products'] = $this->getOrderItems($order);

        return $payUData;
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
