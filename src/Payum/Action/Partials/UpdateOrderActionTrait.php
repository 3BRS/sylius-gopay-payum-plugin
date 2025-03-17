<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action\Partials;

use Payum\Core\Model\ModelAwareInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Api\GoPayApiInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action\GoPayAction;

trait UpdateOrderActionTrait
{
    /**
     * @param \ArrayObject<string, string> $model
     */
    private function updateExistingOrder(
        GoPayApiInterface $gopayApi,
        ModelAwareInterface $request,
        \ArrayObject $model,
    ): void {
        $response = $gopayApi->retrieve($this->getExternalPaymentId($model));

        $recognizedStates = [
            GoPayApiInterface::CREATED,
            GoPayApiInterface::AUTHORIZED,
            GoPayApiInterface::PAID,
            GoPayApiInterface::REFUNDED,
            GoPayApiInterface::CANCELED,
        ];
        if (in_array($response->json['state'], $recognizedStates, true)) {
            $model[GoPayAction::GOPAY_STATUS] = $response->json['state'];
            $request->setModel($model);
        }
    }

    /**
     * @param \ArrayObject<string, string> $model
     */
    private function getExternalPaymentId(\ArrayObject $model): int
    {
        $externalPaymentId = $model[GoPayAction::EXTERNAL_PAYMENT_ID];
        assert(is_numeric($externalPaymentId));

        return (int) $externalPaymentId;
    }
}
