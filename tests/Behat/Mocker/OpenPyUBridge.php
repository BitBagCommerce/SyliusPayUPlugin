<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

declare(strict_types=1);

namespace Tests\BitBag\SyliusPayUPlugin\Behat\Mocker;

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use OpenPayU_Result;
use Psr\Container\ContainerInterface;

final class OpenPyUBridge implements OpenPayUBridgeInterface
{
    /** @var ContainerInterface */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function setAuthorizationData(
        string $environment,
        string $signatureKey,
        string $posId,
        string $clientId,
        string $clientSecret
    ): void {
        $this->container
            ->get('bitbag.payu_plugin.bridge.open_payu')
            ->setAuthorizationData(
                $environment,
                $signatureKey,
                $posId,
                $clientId,
                $clientSecret
            )
        ;
    }

    public function create(array $order): ?OpenPayU_Result
    {
        return $this->container->get('bitbag.payu_plugin.bridge.open_payu')->create($order);
    }

    public function retrieve(string $orderId): OpenPayU_Result
    {
        return $this->container->get('bitbag.payu_plugin.bridge.open_payu')->retrieve($orderId);
    }

    public function consumeNotification($data): ?OpenPayU_Result
    {
        return $this->container->get('bitbag.payu_plugin.bridge.open_payu')->consumeNotification($data);
    }
}
