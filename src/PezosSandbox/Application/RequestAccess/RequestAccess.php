<?php

declare(strict_types=1);

namespace PezosSandbox\Application\RequestAccess;

use PezosSandbox\Domain\Model\Member\Address;

final class RequestAccess
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
