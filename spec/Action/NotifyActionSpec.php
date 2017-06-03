<?php

namespace spec\BitBag\PayUPlugin\Action;

use BitBag\PayUPlugin\Action\NotifyAction;
use BitBag\PayUPlugin\SetPayU;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Request\Notify;
use Payum\Core\Security\TokenInterface;
use PhpSpec\ObjectBehavior;

final class NotifyActionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(NotifyAction::class);
    }

    function it_executes(
        Notify $request,
        TokenInterface $token,
        ArrayObject $model,
        SetPayU $setPayU,
        GetHumanStatus $status,
        GatewayInterface $gateway

    )
    {
        $request->getToken()->willReturn($token);
        $request->getModel()->willReturn($model);
        $setPayU->getToken()->willReturn($token);
        $setPayU->getModel()->willReturn($model);

        $this->setGateway($gateway);
        $this->getGateway()->execute($status);
        $this->getGateway()->execute($setPayU);
    }

    function it_throws_exception_when_model_is_not_array_object(Notify $request)
    {
        $request->getModel()->willReturn(null);

        $this
            ->shouldThrow(RequestNotSupportedException::class)
            ->during('execute', [$request]);
    }
}
