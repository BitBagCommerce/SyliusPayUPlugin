<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

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

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
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
