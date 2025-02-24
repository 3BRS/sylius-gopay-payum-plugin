<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action;

use ArrayObject;
use Payum\Core\Action\ActionInterface;
use Payum\Core\ApiAwareInterface;
use Payum\Core\Bridge\Spl\ArrayObject as PayumArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\Payum;
use Payum\Core\Reply\HttpRedirect;
use Payum\Core\Security\TokenInterface;
use Payum\Core\Storage\IdentityInterface;
use RuntimeException;
use Sylius\Component\Core\Model\CustomerInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Api\GoPayApiInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action\Partials\UpdateOrderActionTrait;
use ThreeBRS\SyliusGoPayPayumPlugin\Payum\GoPayPayumRequest;
use Webmozart\Assert\Assert;

class GoPayAction implements ApiAwareInterface, ActionInterface
{
    use UpdateOrderActionTrait;

    /**
     * @var array{
     *     goid: string,
     *     clientId: string,
     *     clientSecret: string,
     *     isProductionMode: bool,
     * }|array{}
     */
    private array $api = [];

    public function __construct(
        private GoPayApiInterface $goPayApi,
        private Payum $payum,
    ) {
    }

    public function execute(mixed $request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        \assert($request instanceof GoPayPayumRequest);
        $model = PayumArrayObject::ensureArrayObject($request->getModel());

        $this->authorizeGoPayAction($model, $this->goPayApi);

        switch ($request->getTriggeringAction()) {
            case CaptureAction::CAPTURE_ACTION:
                $this->processCapture($request, $model);

                return;
            case RefundAction::REFUND_ACTION:
                $this->processRefund($request, $model);

                return;
            default:
                throw new RuntimeException('Unknown triggering action ' . $request->getTriggeringAction());
        }
    }

    private function processRefund(
        GoPayPayumRequest $request,
        PayumArrayObject $model,
    ): void {
        if (null === $model['orderId'] || null === $model['externalPaymentId']) {
            throw new RuntimeException('Order ID or external payment ID is missing.');
        }

        $response = $this->goPayApi->refund(
            $this->getExternalPaymentId($model),
            $this->getAmount($model),
        );

        if (!isset($response->json['errors']) && GoPayApiInterface::REFUNDED === $response->json['state']) {
            $model['refundId'] = $response->json['id'];
            $request->setModel($model);

            throw new HttpRedirect($response->json['gw_url']);
        }

        throw new RuntimeException('GoPay error: ' . $response->__toString());
    }

    private function getAmount(PayumArrayObject $model): int
    {
        $externalPaymentId = $model['amount'];
        assert(is_numeric($externalPaymentId));

        return (int) $externalPaymentId;
    }

    private function processCapture(
        GoPayPayumRequest $request,
        PayumArrayObject $model,
    ): void {
        if (null === $model['orderId'] || null === $model['externalPaymentId']) {
            $this->createNewOrder($request, $model);

            return;
        }
        $this->updateExistingOrder($this->goPayApi, $request, $model);
    }

    private function createNewOrder(
        GoPayPayumRequest $request,
        PayumArrayObject $model,
    ): void {
        $token = $request->getToken();
        \assert($token instanceof TokenInterface);
        \assert($this->api !== []);
        $order = $this->prepareOrder($token, $model, $this->api['goid']);
        $response = $this->goPayApi->create($order);

        if (!isset($response->json['errors']) && GoPayApiInterface::CREATED === $response->json['state']) {
            $model['orderId'] = $response->json['order_number'];
            $model['externalPaymentId'] = $response->json['id'];
            $request->setModel($model);

            throw new HttpRedirect($response->json['gw_url']);
        }

        throw new RuntimeException('GoPay error: ' . $response->__toString());
    }

    public function supports(mixed $request): bool
    {
        return $request instanceof GoPayPayumRequest &&
               $request->getModel() instanceof ArrayObject;
    }

    public function setApi(mixed $api): void
    {
        if (!is_array($api)) {
            throw new UnsupportedApiException('Not supported.');
        }

        $this->api = $api;
    }

    /**
     * @return array<string, mixed>
     */
    private function prepareOrder(
        TokenInterface $token,
        PayumArrayObject $model,
        string $goid,
    ): array {
        $notifyToken = $this->createNotifyToken($token->getGatewayName(), $token->getDetails());

        $order = [];
        $order['target']['type'] = 'ACCOUNT';
        $order['target']['goid'] = $goid;
        $order['currency'] = $model['currencyCode'];
        $order['amount'] = $model['totalAmount'];
        $order['order_number'] = $model['extOrderId'];
        $order['lang'] = $model['locale'];

        /** @var CustomerInterface $customer */
        $customer = $model['customer'];

        Assert::isInstanceOf(
            $customer,
            CustomerInterface::class,
            sprintf(
                'Make sure the first model is the %s instance.',
                CustomerInterface::class,
            ),
        );

        $payerContact = [
            'email' => (string) $customer->getEmail(),
            'first_name' => (string) $customer->getFirstName(),
            'last_name' => (string) $customer->getLastName(),
        ];

        $order['payer']['contact'] = $payerContact;
        $order['items'] = $this->resolveProducts($model);

        $order['callback']['return_url'] = $token->getTargetUrl();
        $order['callback']['notification_url'] = $notifyToken->getTargetUrl();

        return $order;
    }

    /**
     * @return array<array{name: mixed, amount: mixed}>|array{}
     */
    private function resolveProducts(PayumArrayObject $model): array
    {
        if (false === $model->offsetExists('items') ||
            (is_countable($model['items']) && 0 === count($model['items']))
        ) {
            return [
                [
                    'name' => $model['description'],
                    'amount' => $model['totalAmount'],
                ],
            ];
        }

        return [];
    }

    private function createNotifyToken(
        string $gatewayName,
        IdentityInterface $model,
    ): TokenInterface {
        return $this->payum->getTokenFactory()->createNotifyToken($gatewayName, $model);
    }
}
