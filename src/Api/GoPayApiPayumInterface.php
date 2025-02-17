<?php

declare(strict_types=1);

namespace ThreeBRS\GoPayPayumPlugin\Api;

use GoPay\Http\Response;

interface GoPayApiPayumInterface
{
    public const CREATED = 'CREATED';
    public const PAID = 'PAID';
    public const CANCELED = 'CANCELED';
    public const TIMEOUTED = 'TIMEOUTED';

    public function authorize(string $goId, string $clientId, string $clientSecret, bool $isProductionMode, string $language): void;

    public function create(array $order): Response;

    public function retrieve(int $paymentId): Response;
}
