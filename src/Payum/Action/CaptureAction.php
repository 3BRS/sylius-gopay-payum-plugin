<?php

declare(strict_types=1);

namespace ThreeBRS\GoPayPayumPlugin\Payum\Action;

use ArrayAccess;
use JetBrains\PhpStorm\Pure;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;
use Sylius\Component\Core\Model\OrderInterface;
use ThreeBRS\GoPayPayumPlugin\Payum\GoPayPayumRequest;
use Webmozart\Assert\Assert;

final class CaptureAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    public function execute(mixed $request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        $order = $request->getFirstModel()->getOrder();
        Assert::isInstanceOf($order, OrderInterface::class);
        $model['customer'] = $order->getCustomer();
        $model['locale'] = $this->fallbackLocaleCode($order->getLocaleCode());

        $this->gateway->execute($this->createRequest($request->getToken(), $model));
    }

    #[Pure]
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
