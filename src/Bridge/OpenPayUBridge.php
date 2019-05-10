<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Bridge;

use OpenPayU_Configuration;
use OpenPayU_Order;
use OpenPayU_Result;

final class OpenPayUBridge implements OpenPayUBridgeInterface
{
    public function setAuthorizationDataApi(string $environment, string $signatureKey, string $posId): void
    {
        OpenPayU_Configuration::setEnvironment($environment);
        OpenPayU_Configuration::setSignatureKey($signatureKey);
        OpenPayU_Configuration::setMerchantPosId($posId);
    }

    public function create(array $order)
    {
        return OpenPayU_Order::create($order);
    }

    public function retrieve(string $orderId): OpenPayU_Result
    {
        return OpenPayU_Order::retrieve($orderId);
    }

    public function consumeNotification($data): ?OpenPayU_Result
    {
        return OpenPayU_Order::consumeNotification($data);
    }
}
