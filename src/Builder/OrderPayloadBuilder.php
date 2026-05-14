<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Builder;

use Sylius\Bundle\PayumBundle\Provider\PaymentDescriptionProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\GatewayConfigInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Webmozart\Assert\Assert;

final class OrderPayloadBuilder implements OrderPayloadBuilderInterface
{
    public function __construct(
        private readonly UrlGeneratorInterface $router,
        private readonly PaymentDescriptionProviderInterface $paymentDescriptionProvider,
    ) {
    }

    public function build(PaymentRequestInterface $paymentRequest): array
    {
        /** @var PaymentInterface $payment */
        $payment = $paymentRequest->getPayment();
        /** @var OrderInterface $order */
        $order = $payment->getOrder();
        /** @var CustomerInterface $customer */
        $customer = $order->getCustomer();
        Assert::isInstanceOf($customer, CustomerInterface::class, 'Order is missing a customer.');

        /** @var GatewayConfigInterface $gatewayConfig */
        $gatewayConfig = $paymentRequest->getMethod()->getGatewayConfig();
        $config = $gatewayConfig->getConfig();

        $hash = (string) $paymentRequest->getId();

        $notifyUrl = $this->router->generate(
            'sylius_payment_request_notify',
            ['hash' => $hash],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        $continueUrl = $this->router->generate(
            'sylius_shop_order_after_pay',
            ['hash' => $hash],
            UrlGeneratorInterface::ABSOLUTE_URL,
        );

        $extOrderId = sprintf('%s-%s', (string) $order->getNumber(), substr($hash, 0, 8));

        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();

        $payload = [
            'notifyUrl' => $notifyUrl,
            'continueUrl' => $continueUrl,
            'customerIp' => $order->getCustomerIp() ?? '127.0.0.1',
            'merchantPosId' => (string) ($config['pos_id'] ?? ''),
            'description' => $this->paymentDescriptionProvider->getPaymentDescription($payment),
            'currencyCode' => (string) $order->getCurrencyCode(),
            'totalAmount' => $order->getTotal(),
            'extOrderId' => $extOrderId,
            'buyer' => [
                'email' => (string) $customer->getEmail(),
                'phone' => (string) ($customer->getPhoneNumber() ?? $billingAddress?->getPhoneNumber() ?? ''),
                'firstName' => (string) ($customer->getFirstName() ?: $billingAddress?->getFirstName() ?? ''),
                'lastName' => (string) ($customer->getLastName() ?: $billingAddress?->getLastName() ?? ''),
                'language' => $this->getLanguageCode($order->getLocaleCode()),
            ],
            'products' => $this->buildProducts($order),
        ];

        if ($shippingAddress !== null) {
            $payload['buyer']['delivery'] = [
                'street' => (string) $shippingAddress->getStreet(),
                'postalCode' => (string) $shippingAddress->getPostcode(),
                'city' => (string) $shippingAddress->getCity(),
                'countryCode' => strtoupper((string) $shippingAddress->getCountryCode()),
                'name' => trim($shippingAddress->getFirstName() . ' ' . $shippingAddress->getLastName()),
            ];
        }

        return $payload;
    }

    /**
     * @return array<int, array{name: string, unitPrice: int, quantity: int}>
     */
    private function buildProducts(OrderInterface $order): array
    {
        $products = [];
        $itemsSum = 0;

        /** @var OrderItemInterface $item */
        foreach ($order->getItems() as $item) {
            $itemTotal = $item->getTotal();
            $quantity = $item->getQuantity();

            if (0 === $itemTotal % $quantity) {
                $products[] = [
                    'name' => (string) $item->getProductName(),
                    'unitPrice' => intdiv($itemTotal, $quantity),
                    'quantity' => $quantity,
                ];
            } else {
                $products[] = [
                    'name' => sprintf('%s (x%d)', (string) $item->getProductName(), $quantity),
                    'unitPrice' => $itemTotal,
                    'quantity' => 1,
                ];
            }

            $itemsSum += $itemTotal;
        }

        $difference = $order->getTotal() - $itemsSum;

        if ($difference > 0) {
            $products[] = [
                'name' => 'Shipping and adjustments',
                'unitPrice' => $difference,
                'quantity' => 1,
            ];
        } elseif ($difference < 0) {
            $remaining = -$difference;
            for ($i = count($products) - 1; $i >= 0 && $remaining > 0; --$i) {
                $lineTotal = $products[$i]['unitPrice'] * $products[$i]['quantity'];
                if ($lineTotal > $remaining) {
                    $products[$i]['unitPrice'] = $lineTotal - $remaining;
                    $products[$i]['quantity'] = 1;
                    $remaining = 0;
                } else {
                    $remaining -= $lineTotal;
                    array_splice($products, $i, 1);
                }
            }
        }

        return $products;
    }

    private function getLanguageCode(?string $localeCode): string
    {
        if ($localeCode === null) {
            return 'pl';
        }

        return \Locale::getPrimaryLanguage($localeCode) ?? 'pl';
    }
}
