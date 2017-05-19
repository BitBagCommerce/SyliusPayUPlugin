<?php

namespace BitBag\PayUPlugin\Exception;

use Payum\Core\Exception\Http\HttpException;

class PayUException extends HttpException
{
    const LABEL = 'PayUException';

    public static function newInstance($status)
    {
        $parts = [self::LABEL];

        if (property_exists($status, 'statusLiteral')) {
            $parts[] = '[reason literal] ' . $status->statusLiteral;
        }

        if (property_exists($status, 'statusCode')) {
            $parts[] = '[status code] ' . $status->statusCode;
        }

        if (property_exists($status, 'statusDesc')) {
            $parts[] = '[reason phrase] ' . $status->statusDesc;
        }

        $message = implode(PHP_EOL, $parts);

        $e = new static($message);

        return $e;
    }
}
