<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin;

final class PayUGatewayFactory
{
    public const FACTORY_NAME = 'payu';

    public const FACTORY_LABEL = 'bitbag.payu_plugin.gateway_label';

    public const ENVIRONMENT_SANDBOX = 'sandbox';

    public const ENVIRONMENT_SECURE = 'secure';

    public const STATUS_NEW = 'NEW';

    public const STATUS_PENDING = 'PENDING';

    public const STATUS_COMPLETED = 'COMPLETED';

    public const STATUS_CANCELED = 'CANCELED';

    public const STATUS_WAITING_FOR_CONFIRMATION = 'WAITING_FOR_CONFIRMATION';

    public const STATUS_REJECTED = 'REJECTED';

    public const RESPONSE_STATUS_SUCCESS = 'SUCCESS';
}
