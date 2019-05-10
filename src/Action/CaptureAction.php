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
use BitBag\SyliusPayUPlugin\SetPayU;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /** @var OpenPayUBridgeInterface */
    private $openPayUBridge;

    public function __construct(OpenPayUBridgeInterface $openPayUBridge)
    {
        $this->openPayUBridge = $openPayUBridge;
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

        $payUAction = $this->getPayUAction($request->getToken(), $model);

        $this->getGateway()->execute($payUAction);
    }

    public function supports($request): bool
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }

    public function getGateway(): GatewayInterface
    {
        return $this->gateway;
    }

    private function getPayUAction(TokenInterface $token, ArrayObject $model): SetPayU
    {
        $payUAction = new SetPayU($token);
        $payUAction->setModel($model);

        return $payUAction;
    }

    private function getFallbackLocaleCode(string $localeCode): string
    {
        return explode('_', $localeCode)[0];
    }
}
