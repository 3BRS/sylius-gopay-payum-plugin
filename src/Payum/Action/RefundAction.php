<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Bridge\Spl\ArrayObject as PayumArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Refund;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Api\GoPayApiInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action\Partials\ParseFallbackLocaleCodeTrait;
use ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action\Partials\UpdateOrderActionTrait;
use ThreeBRS\SyliusGoPayPayumPlugin\Payum\GoPayPayumRequest;

final class RefundAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;
    use UpdateOrderActionTrait;
    use ParseFallbackLocaleCodeTrait;

    public const REFUND_ACTION = 'refund';

    public function __construct(
        private GoPayApiInterface $goPayApi,
    ) {
    }

    /**
     * De facto a callback processed on @see Refund request.
     */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        \assert($request instanceof Refund);
        $model = PayumArrayObject::ensureArrayObject($request->getModel());

        $payment = $request->getFirstModel();
        \assert($payment instanceof PaymentInterface);
        $model['amount'] = $payment->getAmount();

        $this->authorizeGoPayAction($model, $this->goPayApi);

        $localeCode = $payment->getOrder()?->getLocaleCode();
        \assert($localeCode !== null);
        $model['locale'] = $this->parseFallbackLocaleCode($localeCode);

        $token = $request->getToken();
        \assert($token instanceof TokenInterface);
        $this->gateway->execute($this->createRequest($token, $model));
    }

    public function supports($request): bool
    {
        return
            $request instanceof Refund &&
            $request->getModel() instanceof \ArrayAccess;
    }

    private function createRequest(
        TokenInterface $token,
        ArrayObject $model,
    ): GoPayPayumRequest {
        $goPayPayumRequest = new GoPayPayumRequest($token);
        $goPayPayumRequest->setModel($model);
        $goPayPayumRequest->setTriggeringAction(self::REFUND_ACTION);

        return $goPayPayumRequest;
    }
}
