<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Api\Dto;

final readonly class OrderStatusResponse
{
    public function __construct(
        public string $orderId,
        public string $status,
        public int $totalAmount,
        public string $currencyCode,
    ) {
    }
}
