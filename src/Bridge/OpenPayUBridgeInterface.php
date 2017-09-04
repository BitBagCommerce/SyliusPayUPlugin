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
 * @author Patryk Drapik <patryk.drapik@bitbag.pl>
 */
interface OpenPayUBridgeInterface
{
    const NEW_API_STATUS = 'NEW';
    const PENDING_API_STATUS = 'PENDING';
    const COMPLETED_API_STATUS = 'COMPLETED';
    const SUCCESS_API_STATUS = 'SUCCESS';
    const CANCELED_API_STATUS = 'CANCELED';

    /**
     * @param $environment
     * @param $signatureKey
     * @param $posId
     */
    public function setAuthorizationDataApi($environment, $signatureKey, $posId);

    /**
     * @param $order
     *
     * @return mixed
     */
    public function create($order);

    /**
     * @param $orderId
     *
     * @return mixed
     */
    public function retrieve($orderId);
}