<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\StateMachine;

use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use ThreeBRS\SyliusGoPayPayumPlugin\Message\Command\RefundPayment;
use Webmozart\Assert\Assert;

final class RefundOrderProcessor implements PaymentStateProcessorInterface
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    public function __invoke(PaymentInterface $payment, string $fromState): void
    {
        /** @var int|null $paymentId */
        $paymentId = $payment->getId();
        Assert::notNull($paymentId, 'Missing payment ID on the payment object');
        $this->commandBus->dispatch(new RefundPayment($paymentId));
    }
}
