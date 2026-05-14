<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Api;

use BitBag\SyliusPayUPlugin\Api\Dto\CreateOrderResponse;
use BitBag\SyliusPayUPlugin\Api\Dto\OrderStatusResponse;
use BitBag\SyliusPayUPlugin\Api\Exception\PayUApiException;

interface PayUClientInterface
{
    /**
     * @param array<string, mixed> $payload
     *
     * @throws PayUApiException
     */
    public function createOrder(array $payload): CreateOrderResponse;

    /**
     * @throws PayUApiException
     */
    public function getOrderStatus(string $orderId): OrderStatusResponse;
}
