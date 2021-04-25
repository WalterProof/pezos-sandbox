<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Member;

use DateTimeImmutable;

final class MemberRequestedAccess
{
    private Address $address;

    private DateTimeImmutable $requestedAt;

    public function __construct(
        Address $address,
        DateTimeImmutable $requestedAt
    ) {
        $this->address     = $address;
        $this->requestedAt = $requestedAt;
    }

    public function address(): Address
    {
        return $this->address;
    }
}
