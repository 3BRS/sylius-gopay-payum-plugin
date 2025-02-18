<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action\Partials;

use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Model\ModelAwareInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Api\GoPayApiInterface;
use Webmozart\Assert\Assert;

trait UpdateOrderActionTrait
{
    /**
     * @var array{
     *     goid: string,
     *     clientId: string,
     *     clientSecret: string,
     *     isProductionMode: bool
     * }|array{}
     */
    private array $api = [];

    public function setApi(mixed $api): void
    {
        if (!is_array($api)) {
            throw new UnsupportedApiException('Not supported.');
        }
        Assert::keyExists($api, 'goid');
        Assert::keyExists($api, 'clientId');
        Assert::keyExists($api, 'clientSecret');
        Assert::keyExists($api, 'isProductionMode');

        $this->api = $api;
    }

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
        GoPayApiInterface $goPayApi,
    ): void {
        \assert(is_string($model['locale']));
        \assert($this->api !== []);

        $goPayApi->authorize(
            $this->api['goid'],
            $this->api['clientId'],
            $this->api['clientSecret'],
            $this->api['isProductionMode'],
            $model['locale'],
        );
    }
}
