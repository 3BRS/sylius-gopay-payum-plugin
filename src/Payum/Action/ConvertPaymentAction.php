<?php

declare(strict_types=1);

namespace ThreeBRS\GoPayPayumPlugin\Payum\Action;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayAwareTrait;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Convert;
use ThreeBRS\GoPayPayumPlugin\Api\GoPayApiInterface;
use Webmozart\Assert\Assert;

final class ConvertPaymentAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    public function execute(mixed $request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $payment = $request->getSource();
        Assert::isInstanceOf($payment, PaymentInterface::class);

        $details = ArrayObject::ensureArrayObject($payment->getDetails());

        $details['totalAmount'] = $payment->getTotalAmount();
        $details['currencyCode'] = $payment->getCurrencyCode();
        $details['extOrderId'] = $payment->getNumber();
        $details['number'] = $payment->getNumber() . date('His');
        $details['description'] = $payment->getDescription();
        $details['client_email'] = $payment->getClientEmail();
        $details['client_id'] = $payment->getClientId();
        $details['status'] = GoPayApiInterface::CREATED;

        $request->setResult((array) $details);
    }

    public function supports(mixed $request): bool
    {
        return $request instanceof Convert &&
               $request->getSource() instanceof PaymentInterface &&
               'array' === $request->getTo();
    }
}
