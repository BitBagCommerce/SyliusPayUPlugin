<?php

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Notification;

use BitBag\SyliusPayUPlugin\Api\Exception\InvalidSignatureException;

final class PayUSignatureVerifier implements PayUSignatureVerifierInterface
{
    public function verify(string $rawBody, string $signatureHeader, string $signatureKey): void
    {
        $parts = $this->parseSignatureHeader($signatureHeader);

        $algorithm = strtoupper($parts['algorithm'] ?? '');
        $receivedSignature = $parts['signature'] ?? '';

        if ($receivedSignature === '') {
            throw new InvalidSignatureException('Missing signature in OpenPayU-Signature header.');
        }

        $expectedSignature = match ($algorithm) {
            'MD5' => md5($rawBody . $signatureKey),
            'SHA-256', 'SHA256' => hash('sha256', $rawBody . $signatureKey),
            default => throw new InvalidSignatureException(sprintf('Unsupported signature algorithm: %s.', $algorithm)),
        };

        if (!hash_equals($expectedSignature, $receivedSignature)) {
            throw new InvalidSignatureException('PayU notification signature mismatch.');
        }
    }

    /**
     * Parses "sender=...;signature=...;algorithm=MD5;content=DOCUMENT" into a key-value array.
     *
     * @return array<string, string>
     */
    private function parseSignatureHeader(string $header): array
    {
        $parts = [];

        foreach (explode(';', rtrim($header, ';')) as $segment) {
            $segment = trim($segment);
            if ($segment === '') {
                continue;
            }

            $pos = strpos($segment, '=');
            if ($pos === false) {
                continue;
            }

            $key = substr($segment, 0, $pos);
            $value = substr($segment, $pos + 1);
            $parts[trim($key)] = trim($value);
        }

        return $parts;
    }
}
