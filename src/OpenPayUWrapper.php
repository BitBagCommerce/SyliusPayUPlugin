<?php

namespace BitBag\PayUPlugin;

final class OpenPayUWrapper
{
    const NEW_API_STATUS = 'NEW';

    const PENDING_API_STATUS = 'PENDING';

    const COMPLETED_API_STATUS = 'COMPLETED';

    const SUCCESS_API_STATUS = 'SUCCESS';

    const CANCELED_API_STATUS = 'CANCELED';

    public function __construct($environment, $signatureKey, $posId)
    {
        \OpenPayU_Configuration::setEnvironment($environment);
        \OpenPayU_Configuration::setMerchantPosId($posId);
        \OpenPayU_Configuration::setSignatureKey($signatureKey);
    }

    public function create($order)
    {
        return \OpenPayU_Order::create($order);
    }

    public function retrieve($orderId)
    {
        return \OpenPayU_Order::retrieve($orderId);
    }
}
