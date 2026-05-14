<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusPayUPlugin\Behat\Mocker;

use BitBag\SyliusPayUPlugin\Api\Dto\CreateOrderResponse;
use BitBag\SyliusPayUPlugin\Api\Dto\OrderStatusResponse;
use BitBag\SyliusPayUPlugin\PayUGatewayFactory;
use Tests\BitBag\SyliusPayUPlugin\Behat\Stub\FakePayUClient;

final class PayUApiMocker
{
    public function __construct(
        private readonly FakePayUClient $fakePayUClient,
    ) {
    }

    public function mockApiSuccessfulPaymentResponse(callable $action): void
    {
        $this->fakePayUClient->setCreateOrderResponse($this->createSuccessfulOrderResponse());

        $action();

        $this->fakePayUClient->reset();
    }

    public function completedPayment(callable $action): void
    {
        $this->fakePayUClient->setCreateOrderResponse($this->createSuccessfulOrderResponse());
        $this->fakePayUClient->setOrderStatusResponse($this->createStatusResponse(PayUGatewayFactory::STATUS_COMPLETED));

        $action();

        $this->fakePayUClient->reset();
    }

    public function canceledPayment(callable $action): void
    {
        $this->fakePayUClient->setCreateOrderResponse($this->createSuccessfulOrderResponse());
        $this->fakePayUClient->setOrderStatusResponse($this->createStatusResponse(PayUGatewayFactory::STATUS_CANCELED));

        $action();

        $this->fakePayUClient->reset();
    }

    private function createSuccessfulOrderResponse(): CreateOrderResponse
    {
        return new CreateOrderResponse(
            orderId: '1',
            redirectUri: '/',
            status: PayUGatewayFactory::RESPONSE_STATUS_SUCCESS,
        );
    }

    private function createStatusResponse(string $status): OrderStatusResponse
    {
        return new OrderStatusResponse(
            orderId: '1',
            status: $status,
            totalAmount: 0,
            currencyCode: 'PLN',
        );
    }
}
