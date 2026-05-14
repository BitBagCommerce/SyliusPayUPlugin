<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusPayUPlugin\Behat\Stub;

use BitBag\SyliusPayUPlugin\Api\Dto\CreateOrderResponse;
use BitBag\SyliusPayUPlugin\Api\Dto\OrderStatusResponse;
use BitBag\SyliusPayUPlugin\Api\PayUClientInterface;

final class FakePayUClient implements PayUClientInterface
{
    private static ?CreateOrderResponse $createOrderResponse = null;

    private static ?OrderStatusResponse $orderStatusResponse = null;

    public function setCreateOrderResponse(CreateOrderResponse $response): void
    {
        self::$createOrderResponse = $response;
    }

    public function setOrderStatusResponse(OrderStatusResponse $response): void
    {
        self::$orderStatusResponse = $response;
    }

    public function reset(): void
    {
        self::$createOrderResponse = null;
        self::$orderStatusResponse = null;
    }

    public function createOrder(array $payload): CreateOrderResponse
    {
        if (null === self::$createOrderResponse) {
            throw new \RuntimeException('FakePayUClient: createOrder response not configured.');
        }

        return self::$createOrderResponse;
    }

    public function getOrderStatus(string $orderId): OrderStatusResponse
    {
        if (null === self::$orderStatusResponse) {
            throw new \RuntimeException('FakePayUClient: getOrderStatus response not configured.');
        }

        return self::$orderStatusResponse;
    }
}
