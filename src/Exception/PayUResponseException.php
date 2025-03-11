<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Exception;

use Exception;

final class PayUResponseException extends Exception
{
    private array $order;

    public function __construct(
        string $message,
        int $code,
        ?array $order = [],
        ?Exception $previous = null,
    ) {
        $this->order = $order;
        parent::__construct($message, $code, $previous);
    }

    public function getOrder(): array
    {
        return $this->order;
    }

    public static function getTranslationByMessage(?string $message): string
    {
        switch ($message) {
            case 'ERROR_INCONSISTENT_CURRENCIES':
                return 'bitbag.payu_plugin.payu_exception.currencies';
            default:
                return 'bitbag.payu_plugin.payu_exception.default';
        }
    }
}
