<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Payum\Request\Factory;

use Payum\Core\Request\Refund;
use Payum\Core\Security\TokenInterface;

interface RefundRequestFactoryInterface extends ModelAggregateFactoryInterface
{
    public function createNewWithToken(TokenInterface $token): Refund;
}
