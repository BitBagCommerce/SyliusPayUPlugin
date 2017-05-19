<?php

namespace BitBag\PayUPlugin;

use BitBag\PayUPlugin\Action\CaptureAction;
use BitBag\PayUPlugin\Action\ConvertPaymentAction;
use BitBag\PayUPlugin\Action\NotifyAction;
use BitBag\PayUPlugin\Action\PayUAction;
use BitBag\PayUPlugin\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

class PayUGatewayFactory extends GatewayFactory
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
            'payum.action.set_payu' => new PayUAction(),
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
