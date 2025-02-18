<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action;

use ArrayAccess;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Payum\GoPayPayumRequest;
use Webmozart\Assert\Assert;

final class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    public function execute(mixed $request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        \assert($request instanceof Capture);
        $model = ArrayObject::ensureArrayObject($request->getModel());

        $payment = $request->getFirstModel();
        \assert($payment instanceof PaymentInterface);
        $order = $payment->getOrder();
        Assert::isInstanceOf($order, OrderInterface::class);
        $model['customer'] = $order->getCustomer();
        $localeCode = $order->getLocaleCode();
        \assert($localeCode !== null);
        $model['locale'] = $this->fallbackLocaleCode($localeCode);

        $token = $request->getToken();
        \assert($token instanceof TokenInterface);
        $this->gateway->execute($this->createRequest($token, $model));
    }

    public function supports(mixed $request): bool
    {
        return $request instanceof Capture &&
               $request->getModel() instanceof ArrayAccess;
    }

    private function createRequest(TokenInterface $token, ArrayObject $model): GoPayPayumRequest
    {
        $goPayPayumRequest = new GoPayPayumRequest($token);
        $goPayPayumRequest->setModel($model);

        return $goPayPayumRequest;
    }

    private function fallbackLocaleCode(string $localeCode): string
    {
        return explode('_', $localeCode)[0];
    }
}
