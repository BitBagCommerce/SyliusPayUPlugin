<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusPayUPlugin\Behat\Mocker;

use BitBag\SyliusPayUPlugin\Api\Dto\CreateOrderResponse;
use BitBag\SyliusPayUPlugin\Api\Dto\OrderStatusResponse;
use BitBag\SyliusPayUPlugin\Api\PayUClientFactoryInterface;
use BitBag\SyliusPayUPlugin\Api\PayUClientInterface;
use BitBag\SyliusPayUPlugin\PayUGatewayFactory;
use Sylius\Behat\Service\Mocker\Mocker;

final class PayUApiMocker
{
    public function __construct(
        private readonly Mocker $mocker,
    ) {
    }

    public function mockApiSuccessfulPaymentResponse(callable $action): void
    {
        $this->mockClientFactory(static function ($client): void {
            $client->shouldReceive('createOrder')->andReturn(self::createResponseSuccessfulApi());
        });

        $action();

        $this->mocker->unmockAll();
    }

    public function completedPayment(callable $action): void
    {
        $this->mockClientFactory(function ($client): void {
            $client->shouldReceive('createOrder')->andReturn(self::createResponseSuccessfulApi());
            $client->shouldReceive('getOrderStatus')->andReturn(
                self::getDataRetrieve(PayUGatewayFactory::STATUS_COMPLETED),
            );
        });

        $action();

        $this->mocker->unmockAll();
    }

    public function canceledPayment(callable $action): void
    {
        $this->mockClientFactory(function ($client): void {
            $client->shouldReceive('createOrder')->andReturn(self::createResponseSuccessfulApi());
            $client->shouldReceive('getOrderStatus')->andReturn(
                self::getDataRetrieve(PayUGatewayFactory::STATUS_CANCELED),
            );
        });

        $action();

        $this->mocker->unmockAll();
    }

    private function mockClientFactory(callable $clientExpectations): void
    {
        $client = $this->mocker->mockService('bitbag.payu_plugin.api.payu_client', PayUClientInterface::class);
        $clientExpectations($client);

        $factory = $this->mocker->mockService(
            'bitbag.payu_plugin.api.payu_client_factory',
            PayUClientFactoryInterface::class,
        );
        $factory->shouldReceive('createForGatewayConfig')->andReturn($client);
    }

    private static function getDataRetrieve(string $statusPayment): OrderStatusResponse
    {
        return new OrderStatusResponse(
            orderId: '1',
            status: $statusPayment,
            totalAmount: 0,
            currencyCode: 'PLN',
        );
    }

    private static function createResponseSuccessfulApi(): CreateOrderResponse
    {
        return new CreateOrderResponse(
            orderId: '1',
            redirectUri: '/',
            status: PayUGatewayFactory::RESPONSE_STATUS_SUCCESS,
        );
    }
}
