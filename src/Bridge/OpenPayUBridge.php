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

use OauthCacheFile;
use OpenPayU_Configuration;
use OpenPayU_Order;
use OpenPayU_Result;

final class OpenPayUBridge implements OpenPayUBridgeInterface
{
    private ?string $cacheDir;

    public function __construct(string $cacheDir = null)
    {
        $this->cacheDir = $cacheDir;
    }

    public function setAuthorizationData(
        string $environment,
        string $signatureKey,
        string $posId,
        string $clientId,
        string $clientSecret
    ): void {
        OpenPayU_Configuration::setEnvironment($environment);

        //set POS ID and Second MD5 Key (from merchant admin panel)
        OpenPayU_Configuration::setMerchantPosId($posId);
        OpenPayU_Configuration::setSignatureKey($signatureKey);

        //set Oauth Client Id and Oauth Client Secret (from merchant admin panel)
        OpenPayU_Configuration::setOauthClientId($clientId);
        OpenPayU_Configuration::setOauthClientSecret($clientSecret);

        OpenPayU_Configuration::setOauthTokenCache(new OauthCacheFile($this->cacheDir));
    }

    public function create(array $order): ?OpenPayU_Result
    {
        /** @var OpenPayU_Result|null $result */
        $result = OpenPayU_Order::create($order);

        return $result;
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
