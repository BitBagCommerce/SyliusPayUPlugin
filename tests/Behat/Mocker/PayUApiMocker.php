<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace Tests\BitBag\SyliusPayUPlugin\Behat\Mocker;

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridge;
use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use OpenPayU_Result;
use Sylius\Behat\Service\Mocker\Mocker;

final class PayUApiMocker
{
    /** @var Mocker */
    private $mocker;

    public function __construct(Mocker $mocker)
    {
        $this->mocker = $mocker;
    }

    public function mockApiSuccessfulPaymentResponse(callable $action): void
    {
        $service = $this->mocker
            ->mockService('bitbag.payu_plugin.bridge.open_payu', OpenPayUBridgeInterface::class);

        $service->shouldReceive('create')->andReturn($this->createResponseSuccessfulApi());
        $service->shouldReceive('setAuthorizationData');

        $action();

        $this->mocker->unmockAll();
    }

    public function completedPayment(callable $action): void
    {
        $service = $this->mocker
            ->mockService('bitbag.payu_plugin.bridge.open_payu', OpenPayUBridgeInterface::class);

        $service->shouldReceive('retrieve')->andReturn(
            $this->getDataRetrieve(OpenPayUBridge::COMPLETED_API_STATUS)
        );
        $service->shouldReceive('create')->andReturn($this->createResponseSuccessfulApi());
        $service->shouldReceive('setAuthorizationData');

        $action();

        $this->mocker->unmockAll();
    }

    public function canceledPayment(callable $action): void
    {
        $service = $this->mocker
            ->mockService('bitbag.payu_plugin.bridge.open_payu', OpenPayUBridgeInterface::class);

        $service->shouldReceive('retrieve')->andReturn(
            $this->getDataRetrieve(OpenPayUBridge::CANCELED_API_STATUS)
        );
        $service->shouldReceive('create')->andReturn($this->createResponseSuccessfulApi());
        $service->shouldReceive('setAuthorizationData');

        $action();

        $this->mocker->unmockAll();
    }

    /**
     * @param $statusPayment
     */
    private function getDataRetrieve($statusPayment): OpenPayU_Result
    {
        $openPayUResult = new OpenPayU_Result();

        $data = (object) [
            'status' => (object) [
                'statusCode' => OpenPayUBridge::SUCCESS_API_STATUS,
            ],
            'orderId' => 1,
            'orders' => [
                (object) [
                    'status' => $statusPayment,
                ],
            ],
        ];

        $openPayUResult->setResponse($data);

        return $openPayUResult;
    }

    private function createResponseSuccessfulApi(): OpenPayU_Result
    {
        $openPayUResult = new OpenPayU_Result();

        $data = (object) [
            'status' => (object) [
                'statusCode' => OpenPayUBridge::SUCCESS_API_STATUS,
            ],
            'orderId' => 1,
            'redirectUri' => '/',
        ];

        $openPayUResult->setResponse($data);

        return $openPayUResult;
    }
}
