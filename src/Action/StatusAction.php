<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\PayUPlugin\Action;

use BitBag\PayUPlugin\OpenPayUWrapper;
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
        $status = $model['status'];

        if (null === $status || OpenPayUWrapper::NEW_API_STATUS === $status) {
            $request->markNew();

            return;
        }

        if (OpenPayUWrapper::PENDING_API_STATUS === $status) {
            $request->markPending();

            return;
        }

        if (OpenPayUWrapper::CANCELED_API_STATUS === $status) {
            $request->markCanceled();

            return;
        }

        if (OpenPayUWrapper::COMPLETED_API_STATUS === $status) {
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
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
