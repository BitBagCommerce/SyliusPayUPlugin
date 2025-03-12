<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusPayUPlugin\Unit\EventListener;

use BitBag\SyliusPayUPlugin\EventListener\PayUResponseExceptionEventListener;
use BitBag\SyliusPayUPlugin\Exception\PayUResponseException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;

final class PayUResponseExceptionEventListenerTest extends TestCase
{
    private PayUResponseExceptionEventListener $listener;

    private MockObject|RouterInterface $router;
    private MockObject|RequestStack $requestStack;
    private MockObject|LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->logger = $this->createMock(LoggerInterface::class);

        $this->listener = new PayUResponseExceptionEventListener(
            $this->router,
            $this->requestStack,
            $this->logger
        );
    }

    public function test_it_redirects_to_order_payment_page_if_token_value_is_set(): void
    {
        $order = ['tokenValue' => 'D8gHAy3dpj12x'];
        $exception = new PayUResponseException('ERROR_INCONSISTENT_CURRENCIES', 500, $order);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ExceptionEvent($kernel, new Request(), 500, $exception);

        $session = $this->createMock(Session::class);
        $flashBag = $this->createMock(FlashBagInterface::class);
        $this->requestStack->method('getSession')->willReturn($session);
        $session->method('getBag')->with('flashes')->willReturn($flashBag);

        $this->router->method('generate')
            ->with('sylius_shop_order_show', ['tokenValue' => $order['tokenValue']])
            ->willReturn(sprintf('/order/%s', $order['tokenValue']));

        $flashBag->expects($this->once())
            ->method('add')
            ->with('error', PayUResponseException::getTranslationByMessage($exception->getMessage()));

        $this->listener->onPayuOpenException($event);
    }

    public function test_it_redirects_to_empty_cart_page_if_token_value_is_not_set(): void
    {
        $order = [];
        $exception = new PayUResponseException('ERROR_INCONSISTENT_CURRENCIES', 500, $order);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ExceptionEvent($kernel, new Request(), 500, $exception);

        $session = $this->createMock(Session::class);
        $flashBag = $this->createMock(FlashBagInterface::class);
        $this->requestStack->method('getSession')->willReturn($session);
        $session->method('getBag')->with('flashes')->willReturn($flashBag);

        $this->router->method('generate')->with('sylius_shop_cart_summary')->willReturn('/cart');

        $flashBag->expects($this->once())
            ->method('add')
            ->with('error', PayUResponseException::getTranslationByMessage($exception->getMessage()));

        $this->listener->onPayuOpenException($event);
    }

    public function test_it_redirects_to_empty_cart_page_if_token_value_is_not_set_and_currency_is_correct(): void
    {
        $order = [];
        $exception = new PayUResponseException('SOME_ERROR', 500, $order);
        $kernel = $this->createMock(HttpKernelInterface::class);
        $event = new ExceptionEvent($kernel, new Request(), 500, $exception);

        $session = $this->createMock(Session::class);
        $flashBag = $this->createMock(FlashBagInterface::class);
        $this->requestStack->method('getSession')->willReturn($session);
        $session->method('getBag')->with('flashes')->willReturn($flashBag);

        $this->router->method('generate')->with('sylius_shop_cart_summary')->willReturn('/cart');

        $flashBag->expects($this->once())
            ->method('add')
            ->with('error', PayUResponseException::getTranslationByMessage($exception->getMessage()));

        $this->listener->onPayuOpenException($event);
    }

    public function test_it_logs_errors(): void
    {
        $exception = new \Exception('An error occurred');

        $this->logger->expects($this->once())
            ->method('error')
            ->with('[PayU] Error while placing order An error occurred');

        $this->listener->logError($exception);
    }
}
