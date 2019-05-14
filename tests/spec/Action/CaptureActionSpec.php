<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusPayUPlugin\Action;

use BitBag\SyliusPayUPlugin\Action\CaptureAction;
use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use Iterator;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

final class CaptureActionSpec extends ObjectBehavior
{
    function let(OpenPayUBridgeInterface $openPayUBridge): void
    {
        $this->beConstructedWith($openPayUBridge);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(CaptureAction::class);
    }

    function it_executes(
        Capture $request,
        ArrayObject $model,
        Iterator $iterator,
        OrderItemInterface $orderItem,
        OrderInterface $order,
        CustomerInterface $customer,
        TokenInterface $token,
        GatewayInterface $gateway
    ): void {
        $model->getIterator()->willReturn($iterator);
        $model->offsetSet('customer', $customer)->willReturn(null);
        $model->offsetSet('locale', 'en')->willReturn(null);
        $request->getModel()->willReturn($model);
        $request->getFirstModel()->willReturn($orderItem);
        $orderItem->getOrder()->willReturn($order);
        $order->getCustomer()->willReturn($customer);
        $order->getLocaleCode()->willReturn('en_US');
        $request->getToken()->willReturn($token);

        $this->setGateway($gateway);
    }

    function it_throws_exception_when_model_is_not_array_object(Capture $request): void
    {
        $request->getModel()->willReturn(null);

        $this
            ->shouldThrow(RequestNotSupportedException::class)
            ->during('execute', [$request]);
    }
}
