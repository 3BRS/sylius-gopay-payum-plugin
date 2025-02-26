<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Message\Command;

abstract class PaymentCommand implements PaymentCommandInterface
{
    public function __construct(private int $paymentId)
    {
    }

    public function getPaymentId(): int
    {
        return $this->paymentId;
    }
}
