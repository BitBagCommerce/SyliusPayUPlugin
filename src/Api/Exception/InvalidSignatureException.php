<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Api\Exception;

final class InvalidSignatureException extends \RuntimeException
{
    public function __construct(
        string $message = 'PayU notification signature is invalid.',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
