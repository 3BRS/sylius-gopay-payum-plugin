<?php

declare(strict_types=1);

namespace ThreeBRS\GoPayPayumPlugin\Payum\Action;

use ArrayObject;
use Exception;
use JetBrains\PhpStorm\Pure;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Notify;
use Sylius\Component\Core\Model\PaymentInterface;
use ThreeBRS\GoPayPayumPlugin\Api\GoPayApiInterface;
use Webmozart\Assert\Assert;

final class NotifyAction implements ActionInterface, ApiAwareInterface
{
    use GatewayAwareTrait, UpdateOrderActionTrait;

    private array $api = [];

    public function __construct(
        private GoPayApiInterface $goPayApi,
    ) {
    }

    public function execute(mixed $request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $payment = $request->getFirstModel();
        Assert::isInstanceOf($payment, PaymentInterface::class);

        $model = $request->getModel();

        $this->goPayApi->authorize(
            $this->api['goid'],
            $this->api['clientId'],
            $this->api['clientSecret'],
            $this->api['isProductionMode'],
            $model['locale'],
        );

        try {
            $this->updateExistingOrder($this->goPayApi, $request, $model);

            throw new HttpResponse('SUCCESS');
        } catch (Exception $e) {
            throw new HttpResponse($e->getMessage());
        }
    }

    #[Pure]
    public function supports(mixed $request): bool
    {
        return $request instanceof Notify && $request->getModel() instanceof ArrayObject;
    }

    public function setApi(mixed $api): void
    {
        if (!is_array($api)) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }
}
