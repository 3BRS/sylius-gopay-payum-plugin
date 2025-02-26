<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusGoPayPayumPlugin\Payum;

use GoPay\Http\Response;
use GoPay\Payments;

class GoPayPaymentsMock extends Payments
{
    private ?int $lastPaymentId = null;
    private ?int $lastAmount = null;

    public function __construct()
    {
        // to disable the constructor of the parent class
    }

    public function refundPayment(
        $id,
        $data,
    ): Response {
        assert(is_int($id), 'Expected int, got ' . gettype($id));
        assert(is_int($data), 'Expected int, got ' . gettype($data));

        $this->lastPaymentId = $id;
        $this->lastAmount = $data;

        /**
         * @see \ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action\GoPayAction::processRefund
         * for expected response
         */
        $data = [
            'state' => 'REFUNDED',
            'id' => 123456,
            'gw_url' => 'https://example.com',
        ];
        $json = json_encode($data);
        $response = new Response($json);
        $response->json = $data;
        $response->statusCode = 200;

        return $response;
    }

    public function getLastPaymentId(): ?int
    {
        return $this->lastPaymentId;
    }

    public function getLastAmount(): ?int
    {
        return $this->lastAmount;
    }
}
