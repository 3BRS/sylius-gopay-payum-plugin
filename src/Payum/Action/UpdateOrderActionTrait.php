<?php

declare(strict_types=1);

namespace ThreeBRS\GoPayPayumPlugin\Payum\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use ThreeBRS\GoPayPayumPlugin\Api\GoPayApiPayumInterface;

trait UpdateOrderActionTrait
{
    private function updateExistingOrder(GoPayApiPayumInterface $gopayApi, mixed $request, ArrayObject $model): void
    {
        $response = $gopayApi->retrieve($model['externalPaymentId']);

        if (GoPayApiPayumInterface::PAID === $response->json['state']) {
            $model['gopayStatus'] = $response->json['state'];
            $request->setModel($model);
        }

        if (GoPayApiPayumInterface::CANCELED === $response->json['state']) {
            $model['gopayStatus'] = $response->json['state'];
            $request->setModel($model);
        }

        if (GoPayApiPayumInterface::TIMEOUTED === $response->json['state']) {
            $model['gopayStatus'] = $response->json['state'];
            $request->setModel($model);
        }

        if (GoPayApiPayumInterface::CREATED === $response->json['state']) {
            $model['gopayStatus'] = GoPayApiPayumInterface::CANCELED;
            $request->setModel($model);
        }
    }
}
