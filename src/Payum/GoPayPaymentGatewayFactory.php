<?php

declare(strict_types=1);

namespace ThreeBRS\GoPayPayumPlugin\Payum;

use GoPay\Definition\Language;
use GoPay\Definition\TokenScope;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use ThreeBRS\GoPayPayumPlugin\Payum\Action\CaptureAction;
use ThreeBRS\GoPayPayumPlugin\Payum\Action\ConvertPaymentAction;
use ThreeBRS\GoPayPayumPlugin\Payum\Action\StatusAction;

class GoPayPaymentGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            'payum.factory_name' => 'gopay',
            'payum.factory_title' => 'GoPay',

            'payum.action.capture' => new CaptureAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.status' => new StatusAction(),
        ]);

        if (!$config['payum.api']) {
            $config['payum.default_options'] = [
                'goid' => '',
                'clientId' => '',
                'clientSecret' => '',
                'isProductionMode' => false,
            ];
            $config->defaults($config['payum.default_options']);

            $config['payum.required_options'] = ['goid', 'clientId', 'clientSecret'];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);

                return [
                    'goid' => $config['goid'],
                    'clientId' => $config['clientId'],
                    'clientSecret' => $config['clientSecret'],
                    'isProductionMode' => $config['isProductionMode'],
                    'scope' => TokenScope::ALL,
                    'language' => Language::ENGLISH,
                    'timeout' => 30,
                ];
            };
        }
    }
}
