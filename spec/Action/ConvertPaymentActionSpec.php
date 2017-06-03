<?php

namespace spec\BitBag\PayUPlugin\Action;

use BitBag\PayUPlugin\Action\ConvertPaymentAction;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;
use PhpSpec\ObjectBehavior;

final class ConvertPaymentActionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ConvertPaymentAction::class);
    }

    function it_implements_action_interface()
    {
        $this->shouldImplement(ActionInterface::class);
    }

    function it_executes(
        Convert $request,
        PaymentInterface $payment
    )
    {
        $request->getSource()->willReturn($payment);
        $request->getTo()->willReturn('array');

        $payment->getTotalAmount()->willReturn(88000);
        $payment->getCurrencyCode()->willReturn('PLN');
        $payment->getNumber()->willReturn(123456);
        $payment->getDescription()->willReturn('Lamborghini Huracan');
        $payment->getClientEmail()->willReturn('mikolaj.krol@bitbag.pl');
        $payment->getClientId()->willReturn(1);
        $_SERVER['REMOTE_ADDR'] = '69.65.13.216';

        $details['totalAmount'] = 88000;
        $details['currencyCode'] = 'PLN';
        $details['extOrderId'] = 123456;
        $details['description'] = 'Lamborghini Huracan';
        $details['client_email'] = 'mikolaj.krol@bitbag.pl';
        $details['client_id'] = '1';
        $details['customerIp'] = '69.65.13.216';
        $details['status'] = 'NEW';

        $request->setResult($details)->shouldBeCalled();

        $this->execute($request);
    }

    function it_throws_exception_when_source_is_not_a_payment_interface(Convert $request)
    {
        $request->getSource()->willReturn(null);

        $this
            ->shouldThrow(RequestNotSupportedException::class)
            ->during('execute', [$request])
        ;
    }
}
