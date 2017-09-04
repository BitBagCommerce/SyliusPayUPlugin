<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\PayUPlugin\Bridge;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
final class OpenPayUBridge implements OpenPayUBridgeInterface
{
    /**
     * {@inheritDoc}
     */
    public function setAuthorizationDataApi($environment, $signatureKey, $posId)
    {
        \OpenPayU_Configuration::setEnvironment($environment);
        \OpenPayU_Configuration::setMerchantPosId($posId);
        \OpenPayU_Configuration::setSignatureKey($signatureKey);
    }

    /**
     * {@inheritDoc}
     */
    public function create($order)
    {
        return \OpenPayU_Order::create($order);
    }

    /**
     * {@inheritDoc}
     */
    public function retrieve($orderId)
    {
        return \OpenPayU_Order::retrieve($orderId);
    }
}
