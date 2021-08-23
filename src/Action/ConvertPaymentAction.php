<?php

/*
 * This file was created by developers working at BitBag
 * Do you need more information about us and what we do? Visit our https://bitbag.io website!
 * We are hiring developers from all over the world. Join us and start your new, exciting adventure and become part of us: https://bitbag.io/career
*/

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Action;

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridge;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;

final class ConvertPaymentAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     *
     * @param Convert $request
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getSource();
        $details = ArrayObject::ensureArrayObject($payment->getDetails());

        $details['totalAmount'] = $payment->getTotalAmount();
        $details['currencyCode'] = $payment->getCurrencyCode();
        $details['extOrderId'] = uniqid((string) $payment->getNumber(), true);
        $details['description'] = $payment->getDescription();
        $details['client_email'] = $payment->getClientEmail();
        $details['client_id'] = $payment->getClientId();
        $details['customerIp'] = $this->getClientIp();
        $details['status'] = OpenPayUBridge::NEW_API_STATUS;

        $request->setResult((array) $details);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request): bool
    {
        return $request instanceof Convert
               && $request->getSource() instanceof PaymentInterface
               && 'array' === $request->getTo();
    }

    private function getClientIp(): ?string
    {
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }
}
