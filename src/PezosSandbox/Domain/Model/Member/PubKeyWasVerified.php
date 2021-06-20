<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Tezos;

use PezosSandbox\Domain\Model\Member\Address;

final class AddressWasVerified
{
    private Address $address;

    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    public function memberAddress(): Address
    {
        return $this->address;
    }
}
