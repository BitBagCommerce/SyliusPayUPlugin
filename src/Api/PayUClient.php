<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Api;

use BitBag\SyliusPayUPlugin\Api\Dto\CreateOrderResponse;
use BitBag\SyliusPayUPlugin\Api\Dto\OrderStatusResponse;
use BitBag\SyliusPayUPlugin\Api\Exception\PayUApiException;
use BitBag\SyliusPayUPlugin\PayUGatewayFactory;
use OpenPayU_Exception;
use OpenPayU_Exception_Request;
use OpenPayU_Order;
use OpenPayU_Result;

final class PayUClient implements PayUClientInterface
{
    public function createOrder(array $payload): CreateOrderResponse
    {
        try {
            /** @var OpenPayU_Result $result */
            $result = OpenPayU_Order::create($payload);
        } catch (OpenPayU_Exception $exception) {
            throw new PayUApiException(
                sprintf('PayU order creation failed: %s', self::buildErrorDetail($exception, $payload)),
                (int) $exception->getCode(),
                $exception,
            );
        }

        $response = $result->getResponse();
        $statusCode = $response->status->statusCode ?? null;

        if ($statusCode !== PayUGatewayFactory::RESPONSE_STATUS_SUCCESS) {
            throw new PayUApiException(sprintf(
                'PayU returned non-success status: %s. Payload: %s',
                $statusCode ?? 'unknown',
                json_encode($payload, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES),
            ));
        }

        return new CreateOrderResponse(
            orderId: (string) $response->orderId,
            redirectUri: (string) $response->redirectUri,
            status: (string) $statusCode,
        );
    }

    /**
     * @param array<string, mixed> $payload
     */
    private static function buildErrorDetail(OpenPayU_Exception $exception, array $payload): string
    {
        $detail = $exception->getMessage();

        if ($exception instanceof OpenPayU_Exception_Request) {
            $original = $exception->getOriginalResponse();
            if ($original !== null) {
                $response = $original->getResponse();
                $status = $response->status ?? null;
                if ($status !== null) {
                    $extras = [];
                    foreach (['code', 'codeLiteral', 'severity', 'statusDesc'] as $field) {
                        if (isset($status->{$field}) && $status->{$field} !== '') {
                            $extras[] = sprintf('%s=%s', $field, (string) $status->{$field});
                        }
                    }
                    if ($extras !== []) {
                        $detail .= ' [' . implode(', ', $extras) . ']';
                    }
                }
            }
        }

        return $detail . ' | Payload: ' . json_encode($payload, \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES);
    }

    public function getOrderStatus(string $orderId): OrderStatusResponse
    {
        try {
            /** @var OpenPayU_Result $result */
            $result = OpenPayU_Order::retrieve($orderId);
        } catch (OpenPayU_Exception $exception) {
            throw new PayUApiException(
                sprintf('PayU order status retrieval failed: %s', $exception->getMessage()),
                (int) $exception->getCode(),
                $exception,
            );
        }

        $response = $result->getResponse();
        $order = $response->orders[0] ?? null;

        if ($order === null) {
            throw new PayUApiException(sprintf('PayU order not found: %s', $orderId));
        }

        return new OrderStatusResponse(
            orderId: (string) $order->orderId,
            status: (string) $order->status,
            totalAmount: (int) $order->totalAmount,
            currencyCode: (string) $order->currencyCode,
        );
    }
}
