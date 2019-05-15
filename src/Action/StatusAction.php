<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

declare(strict_types=1);

namespace BitBag\SyliusPayUPlugin\Action;

use ArrayAccess;
use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Request\GetStatusInterface;

final class StatusAction implements ActionInterface
{
    /** @var OpenPayUBridgeInterface */
    private $openPayUBridge;

    /** @param OpenPayUBridgeInterface $openPayUBridge */
    public function __construct(OpenPayUBridgeInterface $openPayUBridge)
    {
        $this->openPayUBridge = $openPayUBridge;
    }

    /**
     * @throws UnsupportedApiException if the given Api is not supported.
     */
    public function setApi($api): void
    {
        if (false === is_array($api)) {
            throw new UnsupportedApiException('Not supported. Expected to be set as array.');
        }

        $this->openPayUBridge->setAuthorizationData(
            $api['environment'],
            $api['signature_key'],
            $api['pos_id'],
            $api['oauth_client_id'],
            $api['oauth_client_secret']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function execute($request): void
    {
        /** @var $request GetStatusInterface */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $status = $model['statusPayU'] ?? null;
        $orderId = $model['orderId'] ?? null;

        if ((null === $status || OpenPayUBridgeInterface::NEW_API_STATUS === $status) && null !== $orderId) {
            $request->markNew();

            return;
        }

        if (OpenPayUBridgeInterface::PENDING_API_STATUS === $status) {
            $request->markPending();

            return;
        }

        if (OpenPayUBridgeInterface::CANCELED_API_STATUS === $status) {
            $request->markCanceled();

            return;
        }

        if (OpenPayUBridgeInterface::WAITING_FOR_CONFIRMATION_PAYMENT_STATUS === $status) {
            $request->markSuspended();

            return;
        }

        if (OpenPayUBridgeInterface::COMPLETED_API_STATUS === $status) {
            $request->markCaptured();

            return;
        }

        $request->markUnknown();
    }

    public function supports($request): bool
    {
        return $request instanceof GetStatusInterface &&
            $request->getModel() instanceof ArrayAccess
        ;
    }
}
