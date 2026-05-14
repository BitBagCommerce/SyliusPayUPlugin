<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Builder;

use Sylius\Component\Payment\Model\PaymentRequestInterface;

interface OrderPayloadBuilderInterface
{
    /**
     * @return array<string, mixed>
     */
    public function build(PaymentRequestInterface $paymentRequest): array;
}
