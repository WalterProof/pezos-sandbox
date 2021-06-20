<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Domain\Model\Token\Address;

final class AddToken
{
    private string $address;

    public function __construct(string $address)
    {
        $this->address = $address;
    }

    public function address(): Address
    {
        return Address::fromString($this->address);
    }
}
