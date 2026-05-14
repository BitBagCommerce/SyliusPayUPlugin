<?php

declare(strict_types=1);

namespace Tests\BitBag\SyliusPayUPlugin\Behat\Stub;

use BitBag\SyliusPayUPlugin\Api\PayUClientFactoryInterface;
use BitBag\SyliusPayUPlugin\Api\PayUClientInterface;

final class FakePayUClientFactory implements PayUClientFactoryInterface
{
    public function __construct(
        private readonly FakePayUClient $client,
    ) {
    }

    public function createForGatewayConfig(array $config): PayUClientInterface
    {
        return $this->client;
    }
}
