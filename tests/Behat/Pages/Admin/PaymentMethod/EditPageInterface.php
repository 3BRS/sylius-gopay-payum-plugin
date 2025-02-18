<?php

declare(strict_types=1);

namespace Tests\ThreeBRS\SyliusGoPayPayumPlugin\Behat\Pages\Admin\PaymentMethod;

use Sylius\Behat\Page\Admin\Channel\UpdatePageInterface as BaseUpdatePageInterface;

interface EditPageInterface extends BaseUpdatePageInterface
{
    public function setIsProductionMode(bool $value): void;

    public function setGoPayGoId(string $value): void;

    public function setGoPayClientId(string $value): void;

    public function setGoPayClientSecret(string $value): void;
}
