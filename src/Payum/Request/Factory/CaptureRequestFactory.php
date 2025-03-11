<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusGoPayPayumPlugin\Payum\Request\Factory;

use Payum\Core\Request\Capture;
use Payum\Core\Security\TokenInterface;

final class CaptureRequestFactory implements CaptureRequestFactoryInterface
{
    public function createNewWithToken(TokenInterface $token): Capture
    {
        return new Capture($token);
    }
}
