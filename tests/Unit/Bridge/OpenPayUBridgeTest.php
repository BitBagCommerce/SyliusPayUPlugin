<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusPayUPlugin\Unit\Bridge;

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridge;
use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use PHPUnit\Framework\TestCase;

final class OpenPayUBridgeTest extends TestCase
{
    private OpenPayUBridge $bridge;

    protected function setUp(): void
    {
        $this->bridge = new OpenPayUBridge();
    }

    public function testItImplementsPayUBridgeInterface(): void
    {
        $this->assertInstanceOf(OpenPayUBridgeInterface::class, $this->bridge);
    }

    public function testSetAuthorizationData(): void
    {
        $this->bridge->setAuthorizationData(OpenPayUBridgeInterface::SANDBOX_ENVIRONMENT, 'test', 'test', 'test', 'test');
        $this->assertTrue(true);
    }
}

