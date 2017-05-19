<?php

namespace BitBag\PayUPlugin\Action;

use BitBag\PayUPlugin\OpenPayUWrapper;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;

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

        if ($status === null || $status === OpenPayUWrapper::NEW_API_STATUS) {
            $request->markNew();

            return;
        }

        if ($status === OpenPayUWrapper::PENDING_API_STATUS) {
            $request->markPending();

            return;
        }

        if ($status === OpenPayUWrapper::CANCELED_API_STATUS) {
            $request->markCanceled();

            return;
        }

        if ($status === OpenPayUWrapper::COMPLETED_API_STATUS) {
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
