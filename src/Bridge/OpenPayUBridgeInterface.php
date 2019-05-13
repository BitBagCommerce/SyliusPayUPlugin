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

use OpenPayU_Result;

interface OpenPayUBridgeInterface
{
    public const SANDBOX_ENVIRONMENT                     = 'sandbox';
    public const SECURE_ENVIRONMENT                      = 'secure';

    public const NEW_API_STATUS                          = 'NEW';
    public const PENDING_API_STATUS                      = 'PENDING';
    public const COMPLETED_API_STATUS                    = 'COMPLETED';
    public const SUCCESS_API_STATUS                      = 'SUCCESS';
    public const CANCELED_API_STATUS                     = 'CANCELED';
    public const COMPLETED_PAYMENT_STATUS                = 'COMPLETED';
    public const PENDING_PAYMENT_STATUS                  = 'PENDING';
    public const CANCELED_PAYMENT_STATUS                 = 'CANCELED';
    public const WAITING_FOR_CONFIRMATION_PAYMENT_STATUS = 'WAITING_FOR_CONFIRMATION';
    public const REJECTED_STATUS                         = 'REJECTED';

    public function setAuthorizationData(
        string $environment,
        string $signatureKey,
        string $posId,
        string $clientId,
        string $clientSecret
    ): void;

    public function create(array $order): ?OpenPayU_Result;

    public function retrieve(string $orderId): OpenPayU_Result;

    public function consumeNotification($data): ?OpenPayU_Result;
}
