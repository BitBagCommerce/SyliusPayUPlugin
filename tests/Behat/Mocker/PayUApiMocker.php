<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace Tests\BitBag\PayUPlugin\Behat\Mocker;

use BitBag\PayUPlugin\OpenPayUWrapper;
use BitBag\PayUPlugin\OpenPayUWrapperInterface;
use Sylius\Behat\Service\Mocker\Mocker;

/**
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class PayUApiMocker
{
    /**
     * @var Mocker
     */
    private $mocker;

    /**
     * @param Mocker $mocker
     */
    public function __construct(Mocker $mocker)
    {
        $this->mocker = $mocker;
    }

    /**
     * @param callable $action
     */
    public function mockApiSuccessfulPaymentResponse(callable $action)
    {
        $service = $this->mocker
            ->mockService('bitbag.payu.open_payu_wrapper', OpenPayUWrapperInterface::class);

        $service->shouldReceive('create')->andReturn($this->createResponseSuccessfulApi());
        $service->shouldReceive('setAuthorizationDataApi');

        $action();

        $this->mocker->unmockAll();
    }

    /**
     * @param callable $action
     */
    public function completedPayment(callable $action)
    {
        $service = $this->mocker
            ->mockService('bitbag.payu.open_payu_wrapper', OpenPayUWrapperInterface::class);

        $service->shouldReceive('retrieve')->andReturn(
            $this->getDataRetrieve(OpenPayUWrapper::COMPLETED_API_STATUS)
        );
        $service->shouldReceive('create')->andReturn($this->createResponseSuccessfulApi());
        $service->shouldReceive('setAuthorizationDataApi');

        $action();

        $this->mocker->unmockAll();
    }

    /**
     * @param callable $action
     */
    public function canceledPayment(callable $action)
    {
        $service = $this->mocker
            ->mockService('bitbag.payu.open_payu_wrapper', OpenPayUWrapperInterface::class);

        $service->shouldReceive('retrieve')->andReturn(
            $this->getDataRetrieve(OpenPayUWrapper::CANCELED_API_STATUS)
        );
        $service->shouldReceive('create')->andReturn($this->createResponseSuccessfulApi());
        $service->shouldReceive('setAuthorizationDataApi');

        $action();

        $this->mocker->unmockAll();
    }

    /**
     * @param $statusPayment
     *
     * @return \OpenPayU_Result
     */
    private function getDataRetrieve($statusPayment)
    {
        $openPayUResult = new \OpenPayU_Result();

        $data = (object)[
            'status' => (object)[
                'statusCode' => OpenPayUWrapper::SUCCESS_API_STATUS
            ],
            'orderId' => 1,
            'orders' => [
                (object)[
                    'status' => $statusPayment
                ]
            ]
        ];

        $openPayUResult->setResponse($data);

        return $openPayUResult;
    }

    /**
     * @return \OpenPayU_Result
     */
    private function createResponseSuccessfulApi()
    {
        $openPayUResult = new \OpenPayU_Result();

        $data = (object)[
            'status' => (object)[
                'statusCode' => OpenPayUWrapper::SUCCESS_API_STATUS
            ],
            'orderId' => 1,
            'redirectUri' => '/'
        ];

        $openPayUResult->setResponse($data);

        return $openPayUResult;
    }
}