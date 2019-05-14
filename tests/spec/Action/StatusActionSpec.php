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

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use Iterator;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\Capture;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PayumBundle\Request\GetStatus;

final class StatusActionSpec extends ObjectBehavior
{
    function it_marks_as_new_when_status_is_new(
        GetStatus $request,
        ArrayObject $model,
        Iterator $iterator
    ): void {
        $model->getIterator()->willReturn($iterator);
        $model->offsetExists('orderId')->willReturn(true);
        $model->offsetExists('statusPayU')->willReturn(true);
        $model->offsetGet('orderId')->willReturn(1234);
        $model->offsetGet('statusPayU')->willReturn('NEW');

        $request->getModel()->willReturn($model);

        $request->markNew()->shouldBeCalled();

        $this->execute($request);
    }

    function it_marks_as_pending_when_status_form_api_is_pending(
        GetStatus $request,
        ArrayObject $model,
        Iterator $iterator
    ): void {
        $model->getIterator()->willReturn($iterator);
        $model->offsetExists('orderId')->willReturn(true);
        $model->offsetExists('statusPayU')->willReturn(true);
        $model->offsetGet('orderId')->willReturn(1234);
        $model->offsetGet('statusPayU')->willReturn('PENDING');

        $request->getModel()->willReturn($model);

        $request->markPending()->shouldBeCalled();

        $this->execute($request);
    }

    function it_marks_as_canceled_when_status_form_api_is_canceled(
        GetStatus $request,
        ArrayObject $model,
        Iterator $iterator
    ): void {
        $model->getIterator()->willReturn($iterator);
        $model->offsetExists('orderId')->willReturn(true);
        $model->offsetExists('statusPayU')->willReturn(true);
        $model->offsetGet('orderId')->willReturn(1234);
        $model->offsetGet('statusPayU')->willReturn('CANCELED');

        $request->getModel()->willReturn($model);

        $request->markCanceled()->shouldBeCalled();

        $this->execute($request);
    }

    function it_marks_as_waiting_when_status_form_api_is_waiting_for_confirmation(
        GetStatus $request,
        ArrayObject $model,
        Iterator $iterator
    ): void {
        $model->getIterator()->willReturn($iterator);
        $model->offsetExists('orderId')->willReturn(true);
        $model->offsetExists('statusPayU')->willReturn(true);
        $model->offsetGet('orderId')->willReturn(1234);
        $model->offsetGet('statusPayU')->willReturn('WAITING_FOR_CONFIRMATION');

        $request->getModel()->willReturn($model);

        $request->markSuspended()->shouldBeCalled();

        $this->execute($request);
    }

    function it_marks_as_completed_when_status_form_api_is_completed(
        GetStatus $request,
        ArrayObject $model,
        Iterator $iterator
    ): void {
        $model->getIterator()->willReturn($iterator);
        $model->offsetExists('orderId')->willReturn(true);
        $model->offsetExists('statusPayU')->willReturn(true);
        $model->offsetGet('orderId')->willReturn(1234);
        $model->offsetGet('statusPayU')->willReturn('COMPLETED');

        $request->getModel()->willReturn($model);

        $request->markCaptured()->shouldBeCalled();

        $this->execute($request);
    }

    function it_throws_exception_when_model_is_not_array_object(Capture $request): void
    {
        $request->getModel()->willReturn(null);

        $this
            ->shouldThrow(RequestNotSupportedException::class)
            ->during('execute', [$request]);
    }

    function it_implements_action_interface(): void
    {
        $this->shouldImplement(ActionInterface::class);
    }

    function let(OpenPayUBridgeInterface $openPayUBridge): void
    {
        $this->beConstructedWith($openPayUBridge);
    }
}
