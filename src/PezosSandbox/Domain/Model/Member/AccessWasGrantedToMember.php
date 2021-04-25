<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Member;

final class AccessWasGrantedToMember
{
    private Address $address;

    public function __construct(Address $address)
    {
        $this->address = $address;
    }

    public function address(): Address
    {
        return $this->address;
    }
}
