<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\SyliusPayUPlugin\Action;

use BitBag\SyliusPayUPlugin\Bridge\OpenPayUBridgeInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class StatusAction implements ActionInterface
{
    /**
     * {@inheritDoc}
     */
    public function execute($request)
    {
        /** @var $request GetStatusInterface */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $status = isset($model['statusPayU']) ? $model['statusPayU'] : null;

        if ((null === $status || OpenPayUBridgeInterface::NEW_API_STATUS === $status) && false === isset($model['orderId'])) {
            $request->markNew();
            return;
        }

        if (OpenPayUBridgeInterface::PENDING_API_STATUS === $status) {
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

    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
