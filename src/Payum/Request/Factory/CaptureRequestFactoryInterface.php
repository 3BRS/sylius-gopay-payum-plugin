<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Payum\Request\Factory;

use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;

interface CaptureRequestFactoryInterface extends ModelAggregateFactoryInterface
{
    public function createNewWithToken(TokenInterface $token): Capture;
}
