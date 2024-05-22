<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\EventListener;

use BitBag\SyliusPayUPlugin\Exception\PayUResponseException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Routing\RouterInterface;

final class PayUResponseExceptionEventListener
{
    private RouterInterface $router;

    private RequestStack $requestStack;

    private LoggerInterface $logger;

    public function __construct(
        RouterInterface $router,
        RequestStack $requestStack,
        LoggerInterface $logger,
    ) {
        $this->router = $router;
        $this->requestStack = $requestStack;
        $this->logger = $logger;
    }

    public function onPayuOpenException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof PayUResponseException) {
            $message = PayUResponseException::getTranslationByMessage($exception->getMessage());
            $order = $exception->getOrder();

            $this->logError($exception);
            $this->requestStack->getSession()->getBag('flashes')->add('error', $message);

            $route = empty($order['tokenValue'])
                ? $this->router->generate('sylius_shop_cart_summary')
                : $this->router->generate('sylius_shop_order_show', ['tokenValue' => $order['tokenValue']]);

            $response = new RedirectResponse($route);
            $event->setResponse($response);
        }
    }

    public function logError(\Exception $exception): void
    {
        $message = sprintf(
            '%s %s',
            '[PayU] Error while placing order',
            $exception->getMessage(),
        );

        $this->logger->error($message);
    }
}
