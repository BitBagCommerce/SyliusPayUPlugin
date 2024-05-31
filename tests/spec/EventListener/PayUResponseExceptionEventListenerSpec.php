<?php

declare(strict_types=1);

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

namespace spec\BitBag\SyliusPayUPlugin\EventListener;

use BitBag\SyliusPayUPlugin\EventListener\PayUResponseExceptionEventListener;
use BitBag\SyliusPayUPlugin\Exception\PayUResponseException;
use PhpSpec\ObjectBehavior;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\RouterInterface;

final class PayUResponseExceptionEventListenerSpec extends ObjectBehavior
{
    public function let(
        RouterInterface $router,
        RequestStack $requestStack,
        LoggerInterface $logger,
    ): void {
        $this->beConstructedWith($router, $requestStack, $logger);
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(PayUResponseExceptionEventListener::class);
    }

    public function it_should_redirect_to_order_payment_page_if_token_value_is_set(
        RequestStack $requestStack,
        Session $session,
        FlashBagInterface $flashBag,
        RouterInterface $router,
        HttpKernelInterface $kernel,
        RedirectResponse $response,
    ): void {
        $order = ['tokenValue' => 'D8gHAy3dpj12x'];
        $exception = new PayUResponseException('ERROR_INCONSISTENT_CURRENCIES', 500, $order);
        $event = new ExceptionEvent(
            $kernel->getWrappedObject(),
            new Request(),
            500,
            $exception,
        );

        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashBag);
        $router->generate('sylius_shop_order_show', ['tokenValue' => $order['tokenValue']])
            ->willReturn(sprintf('/order/%s', $order['tokenValue']));
        $response->getTargetUrl()->willReturn(sprintf('/order/%s', $order['tokenValue']));

        $flashBag->add('error', PayUResponseException::getTranslationByMessage($exception->getMessage()))
            ->shouldBeCalled();

        $this->onPayuOpenException($event);
    }

    public function it_should_redirect_to_empty_cart_page_if_token_value_is_not_set(
        RequestStack $requestStack,
        Session $session,
        FlashBagInterface $flashBag,
        RouterInterface $router,
        HttpKernelInterface $kernel,
        RedirectResponse $response,
    ): void {
        $order = [];
        $exception = new PayUResponseException('ERROR_INCONSISTENT_CURRENCIES', 500, $order);
        $event = new ExceptionEvent(
            $kernel->getWrappedObject(),
            new Request(),
            500,
            $exception,
        );

        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashBag);
        $router->generate('sylius_shop_cart_summary')->willReturn('/cart');
        $response->getTargetUrl()->willReturn('/cart');

        $flashBag->add('error', PayUResponseException::getTranslationByMessage($exception->getMessage()))
            ->shouldBeCalled();

        $this->onPayuOpenException($event);
    }

    public function it_should_redirect_to_empty_cart_page_if_token_value_is_not_set_and_currency_is_correct(
        RequestStack $requestStack,
        Session $session,
        FlashBagInterface $flashBag,
        RouterInterface $router,
        HttpKernelInterface $kernel,
        RedirectResponse $response,
    ): void {
        $order = [];
        $exception = new PayUResponseException('SOME_ERROR', 500, $order);
        $event = new ExceptionEvent(
            $kernel->getWrappedObject(),
            new Request(),
            500,
            $exception,
        );

        $requestStack->getSession()->willReturn($session);
        $session->getBag('flashes')->willReturn($flashBag);
        $router->generate('sylius_shop_cart_summary')->willReturn('/cart');
        $response->getTargetUrl()->willReturn('/cart');

        $flashBag->add('error', PayUResponseException::getTranslationByMessage($exception->getMessage()))
            ->shouldBeCalled();

        $this->onPayuOpenException($event);
    }

    public function it_logs_errors(LoggerInterface $logger): void
    {
        $exception = new \Exception('An error occurred');
        $logger->error('[PayU] Error while placing order An error occurred')->shouldBeCalled();

        $this->logError($exception);
    }
}
