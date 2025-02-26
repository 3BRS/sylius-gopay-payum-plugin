<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action;

use ArrayAccess;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Api\GoPayApiInterface;

class StatusAction implements ActionInterface
{
    public function execute(mixed $request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        assert($request instanceof GetStatusInterface);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $status = $model['gopayStatus']
            ?? null;

        if ((null === $status || GoPayApiInterface::CREATED === $status) && !isset($model['orderId'])) {
            $request->markNew();

            return;
        }

        if (GoPayApiInterface::REFUNDED === $status) {
            $request->markRefunded();

            return;
        }

        if (GoPayApiInterface::CANCELED === $status) {
            $request->markCanceled();

            return;
        }

        if (GoPayApiInterface::TIMEOUTED === $status) {
            $request->markCanceled();

            return;
        }

        if (GoPayApiInterface::PAID === $status) {
            $request->markCaptured();

            return;
        }

        $request->markUnknown();
    }

    public function supports(mixed $request): bool
    {
        return $request instanceof GetStatusInterface && $request->getModel() instanceof ArrayAccess;
    }
}
