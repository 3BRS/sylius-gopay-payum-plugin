<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Message\CommandHandler;

use Payum\Core\Payum;
use Psr\Log\LoggerInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Api\GoPayApiInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Message\Command\CancelPayment;
use ThreeBRS\SyliusGoPayPayumPlugin\Payum\Action\Exception\PaymentCanNotBeCanceledException;
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
        private LoggerInterface $logger,
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

        try {
            $gateway->execute($cancelRequest);
        } catch (PaymentCanNotBeCanceledException $paymentCanNotBeCanceledException) {
            if ($paymentCanNotBeCanceledException->getGoPayStatus() !== GoPayApiInterface::CREATED) {
                throw $paymentCanNotBeCanceledException;
            }
            // order is canceled, payment is "created", therefore not paid, and we can ignore it without stopping state machine
            $this->logger->warning(
                sprintf(
                    'Payment can not be canceled because is "%s"c. GoPay will cancel it automatically later.',
                    $paymentCanNotBeCanceledException->getGoPayStatus(),
                ),
                ['paymentId' => $payment->getId()],
            );
        }
    }
}
