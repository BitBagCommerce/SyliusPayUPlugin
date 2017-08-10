<?php

/**
 * This file was created by the developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * another great project.
 * You can find more information about us on https://bitbag.shop and write us
 * an email on kontakt@bitbag.pl.
 */

namespace BitBag\PayUPlugin;

use BitBag\PayUPlugin\Action\CaptureAction;
use BitBag\PayUPlugin\Action\ConvertPaymentAction;
use BitBag\PayUPlugin\Action\NotifyAction;
use BitBag\PayUPlugin\Action\PayUAction;
use BitBag\PayUPlugin\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

/**
 * @author Mikołaj Król <mikolaj.krol@bitbag.pl>
 */
final class PayUGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'payu',
            'payum.factory_title' => 'PayU',

            'payum.action.capture' => new CaptureAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.notify' => new NotifyAction()
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = [
                'environment' => 'secure',
                'pos_id' => '',
                'signature_key' => ''
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = ['environment', 'pos_id', 'signature_key'];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                $payuConfig = [
                    'environment' => $config['environment'],
                    'pos_id' => $config['pos_id'],
                    'signature_key' => $config['signature_key'],
                ];

                return $payuConfig;
            };
        }
    }
}
