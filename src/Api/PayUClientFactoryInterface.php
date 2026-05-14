<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Api;

interface PayUClientFactoryInterface
{
    /**
     * @param array<string, mixed> $config
     */
    public function createForGatewayConfig(array $config): PayUClientInterface;
}
