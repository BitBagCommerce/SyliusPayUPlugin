<?php

namespace BitBag\PayUPlugin\Action;

use BitBag\PayUPlugin\SetPayU;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\GetHumanStatus;
use Payum\Core\Request\Notify;

final class NotifyAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @param mixed $request
     *
     * @throws \Payum\Core\Exception\RequestNotSupportedException if the action dose not support the request.
     */
    public function execute($request)
    {
        /** @var $request Notify */
        RequestNotSupportedException::assertSupports($this, $request);
        $setPayU = new SetPayU($request->getToken());
        $setPayU->setModel($request->getModel());;
        $this->gateway->execute($setPayU);
        $status = new GetHumanStatus($request->getToken());
        $status->setModel($request->getModel());
        $this->gateway->execute($status);
    }


    /**
     * {@inheritDoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Notify &&
            $request->getModel() instanceof \ArrayObject
            ;
    }
}
