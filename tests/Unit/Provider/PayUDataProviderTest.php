<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusPayUPlugin\Unit\Provider;

use BitBag\SyliusPayUPlugin\Api\Provider\PayUDataProvider;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\OrderPay\Provider\UrlProviderInterface;
use Sylius\Bundle\PayumBundle\Provider\PaymentDescriptionProviderInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class PayUDataProviderTest extends TestCase
{
    private PayUDataProvider $payUDataProvider;
    private UrlProviderInterface $urlProvider;
    private PaymentDescriptionProviderInterface $paymentDescriptionProvider;

    protected function setUp(): void
    {
        $this->urlProvider = $this->createMock(UrlProviderInterface::class);
        $this->paymentDescriptionProvider = $this->createMock(PaymentDescriptionProviderInterface::class);

        $this->payUDataProvider = new PayUDataProvider(
            $this->urlProvider,
            $this->paymentDescriptionProvider
        );
    }

    public function testPreparePayUCreateData(): void
    {
        $paymentRequest = $this->createMock(PaymentRequestInterface::class);
        $payment = $this->createMock(PaymentInterface::class);
        $order = $this->createMock(OrderInterface::class);
        $customer = $this->createMock(CustomerInterface::class);

        $paymentRequest->method('getPayment')->willReturn($payment);
        $payment->method('getOrder')->willReturn($order);
        $order->method('getCustomerIp')->willReturn('127.0.0.1');
        $order->method('getCurrencyCode')->willReturn('PLN');
        $order->method('getTotal')->willReturn(10000);
        $order->method('getTokenValue')->willReturn('test-token');
        $order->method('getCustomer')->willReturn($customer);
        $order->method('getLocaleCode')->willReturn('pl_PL');

        $customer->method('getEmail')->willReturn('test@example.com');
        $customer->method('getFirstName')->willReturn('John');
        $customer->method('getLastName')->willReturn('Doe');

        $this->urlProvider
            ->method('getUrl')
            ->willReturn('https://example.com/continue');

        $this->paymentDescriptionProvider
            ->method('getPaymentDescription')
            ->willReturn('Test payment');

        $result = $this->payUDataProvider->preparePayUCreateData($paymentRequest);

        $this->assertSame('https://example.com/continue', $result['continueUrl']);
        $this->assertSame('127.0.0.1', $result['customerIp']);
        $this->assertSame('PLN', $result['currencyCode']);
        $this->assertSame(10000, $result['totalAmount']);
        $this->assertSame('test-token', $result['tokenValue']);
        $this->assertSame(
            [
                'email' => 'test@example.com',
                'firstName' => 'John',
                'lastName' => 'Doe',
                'language' => 'pl'
            ],
            $result['buyer']
        );
    }
}
