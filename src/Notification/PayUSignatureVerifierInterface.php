<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Notification;

use BitBag\SyliusPayUPlugin\Api\Exception\InvalidSignatureException;

interface PayUSignatureVerifierInterface
{
    /**
     * @throws InvalidSignatureException
     */
    public function verify(string $rawBody, string $signatureHeader, string $signatureKey): void;
}
