<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Api;

use BitBag\SyliusPayUPlugin\PayUGatewayFactory;
use OauthCacheFile;
use OpenPayU_Configuration;

final class PayUClientFactory implements PayUClientFactoryInterface
{
    public function __construct(
        private readonly ?string $cacheDir = null,
    ) {
    }

    public function createForGatewayConfig(array $config): PayUClientInterface
    {
        $environment = (string) ($config['environment'] ?? PayUGatewayFactory::ENVIRONMENT_SANDBOX);

        OpenPayU_Configuration::setEnvironment($environment);
        OpenPayU_Configuration::setMerchantPosId((string) ($config['pos_id'] ?? ''));
        OpenPayU_Configuration::setSignatureKey((string) ($config['signature_key'] ?? ''));
        OpenPayU_Configuration::setOauthClientId((string) ($config['oauth_client_id'] ?? ''));
        OpenPayU_Configuration::setOauthClientSecret((string) ($config['oauth_client_secret'] ?? ''));

        OpenPayU_Configuration::setOauthTokenCache(new OauthCacheFile($this->cacheDir));

        return new PayUClient();
    }
}
