<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action\Partials;

use Payum\Core\Model\ModelAwareInterface;
use Psr\Log\LoggerInterface;
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

        /**
         * https://doc.gopay.com/#payment-states-and-expiration
         */
        $recognizedStates = [
            GoPayApiInterface::CREATED,
            GoPayApiInterface::PAYMENT_METHOD_CHOSEN,
            GoPayApiInterface::TIMEOUTED,
            GoPayApiInterface::PAID,
            GoPayApiInterface::CANCELED,
            GoPayApiInterface::AUTHORIZED,
            GoPayApiInterface::PARTIALLY_REFUNDED,
            GoPayApiInterface::REFUNDED,
        ];
        if (in_array($response->json['state'], $recognizedStates, true)) {
            $model[GoPayAction::GOPAY_STATUS] = $response->json['state'];
            $request->setModel($model);
        } else {
            $this->getLogger()->warning(sprintf("Unknown GoPay state: '%s'", $response->json['state']));
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

    abstract protected function getLogger(): LoggerInterface;
}
