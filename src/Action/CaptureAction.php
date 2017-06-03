<?php

namespace BitBag\PayUPlugin\Action;

use BitBag\PayUPlugin\SetPayU;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;

final class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function execute($request)
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = $request->getModel();
        ArrayObject::ensureArrayObject($model);

        $model['customer'] = $request->getFirstModel()->getOrder()->getCustomer();

        $payUAction = $this->getPayUAction($request->getToken(), $model);

        $this->getGateway()->execute($payUAction);
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof Capture &&
            $request->getModel() instanceof \ArrayAccess
            ;
    }

    /**
     * @return \Payum\Core\GatewayInterface
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param TokenInterface $token
     * @param ArrayObject $model
     * @return SetPayU
     */
    private function getPayUAction(TokenInterface $token, ArrayObject $model)
    {
        $payUAction = new SetPayU($token);
        $payUAction->setModel($model);

        return $payUAction;
    }
}