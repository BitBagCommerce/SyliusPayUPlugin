<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Processor;

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentTransitions;

final readonly class PaymentTransitionProcessor implements PaymentTransitionProcessorInterface
{
    public function __construct(
        private StateMachineInterface $stateMachine,
        private OpenPayUBridgeInterface $openPayUBridge,
    ) {
    }

    public function process(PaymentRequestInterface $paymentRequest): void
    {
        $payment = $paymentRequest->getPayment();
        $orderId = $payment->getDetails()['orderId'];
        $transition = $this->getTransition($orderId);

        if (null === $transition) {
            return;
        }

        if ($this->stateMachine->can($payment, PaymentTransitions::GRAPH, $transition)) {
            $this->stateMachine->apply($payment, PaymentTransitions::GRAPH, $transition);
        }
    }

    private function getTransition(?string $orderId): ?string
    {
        $payUResult = $this->openPayUBridge->retrieve($orderId);
        $response = $payUResult->getResponse();
        $payUOrder = reset($response->orders);
        /** @var ?string $status */
        $status = $payUOrder->status;

        return match ($status) {
            PaymentTransitionProcessorInterface::STATE_NEW => PaymentTransitions::TRANSITION_PROCESS,
            PaymentTransitionProcessorInterface::STATE_CANCELED => PaymentTransitions::TRANSITION_CANCEL,
            PaymentTransitionProcessorInterface::STATE_COMPLETED => PaymentTransitions::TRANSITION_COMPLETE,
            default => null
        };
    }
}
