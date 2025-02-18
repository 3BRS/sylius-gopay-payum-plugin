<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action;

use ArrayObject;
use Exception;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject as PayumArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\Notify;
use Sylius\Component\Core\Model\PaymentInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Api\GoPayApiInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action\Partials\UpdateOrderActionTrait;
use Webmozart\Assert\Assert;

final class NotifyAction implements ActionInterface, ApiAwareInterface
{
    use GatewayAwareTrait;
    use UpdateOrderActionTrait;

    /**
     * @var array{
     *     goid: string,
     *     clientId: string,
     *     clientSecret: string,
     *     isProductionMode: bool
     * }|array{}
     */
    private array $api = [];

    public function __construct(
        private GoPayApiInterface $goPayApi,
    ) {
    }

    public function execute(mixed $request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        \assert($request instanceof Notify);
        $model = PayumArrayObject::ensureArrayObject($request->getModel());

        $payment = $request->getFirstModel();
        Assert::isInstanceOf($payment, PaymentInterface::class);

        $this->authorizeGoPayAction($model);

        try {
            $this->updateExistingOrder($this->goPayApi, $request, $model);

            throw new HttpResponse('SUCCESS');
        } catch (Exception $e) {
            throw new HttpResponse($e->getMessage());
        }
    }

    public function supports(mixed $request): bool
    {
        return $request instanceof Notify && $request->getModel() instanceof ArrayObject;
    }

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
}
