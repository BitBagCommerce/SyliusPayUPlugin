<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
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

    public function getOrder()
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
