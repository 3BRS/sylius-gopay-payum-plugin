<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action\Partials;

use Payum\Core\Model\ModelAwareInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Api\GoPayApiInterface;

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

        if (GoPayApiInterface::PAID === $response->json['state']) {
            $model['gopayStatus'] = $response->json['state'];
            $request->setModel($model);
        }

        if (GoPayApiInterface::CANCELED === $response->json['state']) {
            $model['gopayStatus'] = $response->json['state'];
            $request->setModel($model);
        }

        if (GoPayApiInterface::TIMEOUTED === $response->json['state']) {
            $model['gopayStatus'] = $response->json['state'];
            $request->setModel($model);
        }

        if (GoPayApiInterface::CREATED === $response->json['state']) {
            $model['gopayStatus'] = GoPayApiInterface::CANCELED;
            $request->setModel($model);
        }
    }

    /**
     * @param \ArrayObject<string, string> $model
     */
    private function getExternalPaymentId(\ArrayObject $model): int
    {
        $externalPaymentId = $model['externalPaymentId'];
        assert(is_numeric($externalPaymentId));

        return (int) $externalPaymentId;
    }

    /**
     * @param \ArrayObject<string, string> $model
     */
    private function authorizeGoPayAction(
        \ArrayAccess $model,
    ): void {
        \assert(is_string($model['locale']));
        \assert($this->api !== []);

        $this->goPayApi->authorize(
            $this->api['goid'],
            $this->api['clientId'],
            $this->api['clientSecret'],
            $this->api['isProductionMode'],
            $model['locale'],
        );
    }
}
