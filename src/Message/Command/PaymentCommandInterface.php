<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Message\Command;

interface PaymentCommandInterface
{
    public function getPaymentId(): int;
}
