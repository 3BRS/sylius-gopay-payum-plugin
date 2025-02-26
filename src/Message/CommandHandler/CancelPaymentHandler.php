<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Message\CommandHandler;

use Payum\Core\Payum;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Message\Command\CancelPayment;
use ThreeBRS\SyliusGoPayPayumPlugin\Payum\Request\Factory\CancelRequestFactoryInterface;

final class CancelPaymentHandler extends AbstractPayumPaymentHandler
{
    /**
     * @param string[] $supportedGateways
     */
    public function __construct(
        private CancelRequestFactoryInterface $cancelRequestFactory,
        private Payum $payum,
        PaymentRepositoryInterface $paymentRepository,
        array $supportedGateways = ['gopay'],
    ) {
        parent::__construct($paymentRepository, $payum, $supportedGateways);
    }

    public function __invoke(CancelPayment $command): void
    {
        $payment = $this->getPayment($command);
        if (null === $payment) {
            return;
        }

        if (0 === count($payment->getDetails())) {
            return;
        }

        $details = $payment->getDetails();
        if (isset($details['expires_at']) && $details['expires_at'] <= (new \DateTime())->getTimestamp()) {
            return;
        }

        $gatewayName = $this->getGatewayNameFromPayment($payment);

        if (null === $gatewayName) {
            return;
        }

        $gateway = $this->payum->getGateway($gatewayName);
        $token = $this->buildToken($gatewayName, $payment);

        $cancelRequest = $this->cancelRequestFactory->createNewWithToken($token);
        $gateway->execute($cancelRequest);
    }
}
