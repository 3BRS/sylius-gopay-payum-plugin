<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Api;

use GoPay\Api;
use GoPay\Definition\Language;
use GoPay\Definition\TokenScope;
use GoPay\Http\Response;
use GoPay\Payments;

final class GoPayApi implements GoPayApiInterface
{
    public const PRODUCTION_GATEWAY_URL = 'https://gate.gopay.cz/api';

    public const SANDBOX_TEST_GATEWAY_URL = 'https://gw.sandbox.gopay.com/api';

    private Payments $gopay;

    /**
     * For supported languages @see \GoPay\Definition\Language
     *
     * For client ID and client secret see @see https://doc.gopay.com/#access-token
     *
     * For gateway URL see @see https://help.gopay.com/en/s/uY
     */
    public function authorize(
        string $goId,
        string $clientId,
        string $clientSecret,
        bool $isProductionMode,
        string $language = Language::ENGLISH,
        ?string $gatewayUrl = null,
        string $scope = TokenScope::ALL,
        int $timeout = 30,
    ): void {
        $this->gopay = Api::payments([
            'goid' => $goId,
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'gatewayUrl' => $this->getGatewayUrl($isProductionMode, $gatewayUrl),
            'isProductionMode' => $isProductionMode,
            'language' => $language,
            'scope' => $scope,
            'timeout' => $timeout,
        ]);
    }

    private function getGatewayUrl(
        bool $isProductionMode,
        ?string $gatewayUrl,
    ): string {
        if ($gatewayUrl !== null) {
            return $gatewayUrl;
        }

        return $isProductionMode
            ? self::PRODUCTION_GATEWAY_URL
            : self::SANDBOX_TEST_GATEWAY_URL;
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
    public function refund(
        int $paymentId,
        int $amount,
    ): Response {
        return $this->gopay->refundPayment($paymentId, $amount);
    }
}
