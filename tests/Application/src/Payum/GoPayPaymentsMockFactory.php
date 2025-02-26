<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusGoPayPayumPlugin\Payum;

use GoPay\Definition\Language;
use GoPay\Definition\TokenScope;
use GoPay\Payments;
use ThreeBRS\SyliusGoPayPayumPlugin\Payum\GoPayPaymentsFactoryInterface;

class GoPayPaymentsMockFactory implements GoPayPaymentsFactoryInterface
{
    private static ?GoPayPaymentsMock $lastPayments = null;

    public function createPayments(
        string  $goId,
        string  $clientId,
        string  $clientSecret,
        bool    $isProductionMode,
        string  $language = Language::ENGLISH,
        ?string $gatewayUrl = null,
        string  $scope = TokenScope::ALL,
        int     $timeout = 30,
    ): Payments {
        $payments = new GoPayPaymentsMock();
        self::$lastPayments = $payments;

        return $payments;
    }

    public function getLastPayments(): ?GoPayPaymentsMock
    {
        return self::$lastPayments;
    }
}
