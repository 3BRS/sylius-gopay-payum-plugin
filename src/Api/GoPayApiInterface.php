<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Api;

use GoPay\Definition\Language;
use GoPay\Definition\TokenScope;
use GoPay\Http\Response;

interface GoPayApiInterface
{
    public const CREATED = 'CREATED';

    public const PAID = 'PAID';

    public const REFUNDED = 'REFUNDED';

    public const CANCELED = 'CANCELED';

    public const TIMEOUTED = 'TIMEOUTED';

    /**
     * payment was finalized, for example by a refund
     *
     * @noinspection PhpUnused
     */
    public const FINISHED = 'FINISHED';

    public function authorize(
        string $goId,
        string $clientId,
        string $clientSecret,
        bool $isProductionMode,
        string $language = Language::ENGLISH,
        ?string $gatewayUrl = null,
        string $scope = TokenScope::ALL,
        int $timeout = 30,
    ): void;

    /**
     * @param array<string, mixed> $order
     */
    public function create(array $order): Response;

    public function retrieve(int $paymentId): Response;

    public function refund(
        int $paymentId,
        int $amount,
    ): Response;
}
