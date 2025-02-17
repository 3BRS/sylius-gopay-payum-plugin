<?php

declare(strict_types=1);

namespace ThreeBRS\GoPayPayumPlugin\Action;

use ArrayAccess;
use ThreeBRS\GoPayPayumPlugin\Api\GoPayApiPayumInterface;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;

class StatusAction implements ActionInterface
{
    public function execute(mixed $request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $status = isset($model['gopayStatus']) ? $model['gopayStatus'] : null;

        if ((null === $status || GoPayApiPayumInterface::CREATED === $status) && false === isset($model['orderId'])) {
            $request->markNew();
            return;
        }

        if (GoPayApiPayumInterface::CANCELED === $status) {
            $request->markCanceled();
            return;
        }

        if (GoPayApiPayumInterface::TIMEOUTED === $status) {
            $request->markCanceled();
            return;
        }

        if (GoPayApiPayumInterface::PAID === $status) {
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
