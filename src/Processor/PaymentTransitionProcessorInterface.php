<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Processor;

use OpenPayU_Result;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

interface PaymentTransitionProcessorInterface
{

    public const STATE_NEW = 'NEW';

    public const STATE_CANCELED = 'CANCELED';

    public const STATE_COMPLETED = 'COMPLETED';

    public function process(PaymentRequestInterface $paymentRequest): void;
}
