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

use BitBag\SyliusPayUPlugin\Action\ConvertPaymentAction;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\OrderInterface;

final class ConvertPaymentActionSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(ConvertPaymentAction::class);
    }

    function it_implements_action_interface(): void
    {
        $this->shouldImplement(ActionInterface::class);
    }

    function it_executes(
        Convert $request,
        PaymentInterface $payment,
        OrderInterface $order
    ): void {
        $payment->getDetails()->willReturn([]);
        $payment->getTotalAmount()->willReturn(88000);
        $payment->getCurrencyCode()->willReturn('PLN');
        $payment->getNumber()->willReturn(123456);
        $payment->getDescription()->willReturn('Lamborghini Huracan');
        $payment->getClientEmail()->willReturn('test@bitbag.pl');
        $payment->getClientId()->willReturn('1');

        $request->getSource()->willReturn($payment);
        $request->getTo()->willReturn('array');

        $_SERVER['REMOTE_ADDR'] = '69.65.13.216';

        $details['totalAmount'] = 88000;
        $details['currencyCode'] = 'PLN';
        $details['extOrderId'] = 123456;
        $details['description'] = 'Lamborghini Huracan';
        $details['client_email'] = 'test@bitbag.pl';
        $details['client_id'] = '1';
        $details['customerIp'] = '69.65.13.216';
        $details['status'] = 'NEW';

        $request->setResult(
            Argument::that(
                static function ($values) use ($details): bool {
                    foreach ($values as $key => $value) {
                        if ('extOrderId' !== $key && $value !== $details[$key]) {
                            return false;
                        }
                    }

                    return true;
                }
            )
        )->shouldBeCalled();

        $this->execute($request);
    }

    function it_throws_exception_when_source_is_not_a_payment_interface(Convert $request): void
    {
        $request->getSource()->willReturn(null);

        $this
            ->shouldThrow(RequestNotSupportedException::class)
            ->during('execute', [$request]);
    }
}
