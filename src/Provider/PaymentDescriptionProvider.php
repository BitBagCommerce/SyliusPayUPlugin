<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Provider;

use Sylius\Bundle\PayumBundle\Provider\PaymentDescriptionProviderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class PaymentDescriptionProvider implements PaymentDescriptionProviderInterface
{
    public function getPaymentDescription(PaymentInterface $payment): string
    {
        $order = $payment->getOrder();

        return $order->getNumber();
    }
}
