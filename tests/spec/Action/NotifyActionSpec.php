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

use BitBag\SyliusPayUPlugin\Action\NotifyAction;
use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Request\Notify;
use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;

final class NotifyActionSpec extends ObjectBehavior
{
    function let(OpenPayUBridgeInterface $openPayUBridge): void
    {
        $this->beConstructedWith($openPayUBridge);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(NotifyAction::class);
    }

    function it_executes(
        Notify $request,
        TokenInterface $token,
        ArrayObject $model,
        GetHumanStatus $status,
        GatewayInterface $gateway
    ): void {
        $request->getToken()->willReturn($token);
        $request->getModel()->willReturn($model);

        $this->setGateway($gateway);
    }

    function it_throws_exception_when_model_is_not_array_object(Notify $request): void
    {
        $request->getModel()->willReturn(null);

        $this
            ->shouldThrow(RequestNotSupportedException::class)
            ->during('execute', [$request]);
    }
}
