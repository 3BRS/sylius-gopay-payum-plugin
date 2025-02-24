<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Api;

use GoPay\Http\Response;

interface GoPayApiInterface
{
    public const CREATED = 'CREATED';

    public const PAID = 'PAID';

    public const REFUNDED = 'REFUNDED';

    public const CANCELED = 'CANCELED';

    public const TIMEOUTED = 'TIMEOUTED';

    public function authorize(
        string $goId,
        string $clientId,
        string $clientSecret,
        bool $isProductionMode,
        string $language,
    ): void;

    /**
     * @param array<string, mixed> $order
     */
    public function create(array $order): Response;

    public function retrieve(int $paymentId): Response;

    public function refund(int $paymentId, int $amount): Response;
}
