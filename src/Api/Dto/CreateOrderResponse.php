<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Api\Dto;

final readonly class CreateOrderResponse
{
    public function __construct(
        public string $orderId,
        public string $redirectUri,
        public string $status,
    ) {
    }
}
