<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Payum\Request\Factory;

use Payum\Core\Model\ModelAggregateInterface;
use Payum\Core\Security\TokenInterface;

interface ModelAggregateFactoryInterface
{
    public function createNewWithToken(TokenInterface $token): ModelAggregateInterface;
}
