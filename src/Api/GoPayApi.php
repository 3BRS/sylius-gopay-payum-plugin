<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Api;

use GoPay\Api;
use GoPay\Http\Response;
use GoPay\Payments;

final class GoPayApi implements GoPayApiInterface
{
    private Payments $gopay;

    public function authorize(
        string $goId,
        string $clientId,
        string $clientSecret,
        bool $isProductionMode,
        string $language,
    ): void {
        $this->gopay = Api::payments([
            'goid' => $goId,
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'isProductionMode' => $isProductionMode,
            'language' => $language,
        ]);
    }

    /**
     * @param array<string, mixed> $order
     */
    public function create(array $order): Response
    {
        return $this->gopay->createPayment($order);
    }

    public function retrieve(int $paymentId): Response
    {
        return $this->gopay->getStatus($paymentId);
    }

    /**
     * Note: refund requires GoPay token with scope=payment-all
     *
     * @see https://help.gopay.com/en/knowledge-base/integration-of-payment-gateway/integration-of-payment-gateway-1/refunds
     *
     * @param int $amount Use full price to refund the whole payment, or partial amount to do partial refund (partial refund can be done only after 24 hours from the payment)
     */
    public function refund(int $paymentId, int $amount): Response
    {
        return $this->gopay->refundPayment($paymentId, $amount);
    }
}
