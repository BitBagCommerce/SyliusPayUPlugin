<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

declare(strict_types=1);

namespace spec\BitBag\SyliusPayUPlugin\Bridge;

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use PhpSpec\ObjectBehavior;

final class OpenPayUBridgeSpec extends ObjectBehavior
{
    function it_implements_payu_bridge_interface(): void
    {
        $this->shouldHaveType(OpenPayUBridgeInterface::class);
    }

    function it_sets_authorization_data(): void
    {
        $this->setAuthorizationData(OpenPayUBridgeInterface::SANDBOX_ENVIRONMENT, 'test', 'test', 'test', 'test');
    }
}
