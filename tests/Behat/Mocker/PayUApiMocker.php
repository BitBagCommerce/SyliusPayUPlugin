<?php

declare(strict_types=1);

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

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
