<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Domain\Model\Member\MemberRequestedAccess;
use PezosSandbox\Domain\Model\Tezos\AddressWasVerified;

final class AccessPolicy
{
    private ApplicationInterface $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    public function whenMemberRequestedAccess(
        MemberRequestedAccess $event
    ): void {
        $this->application->verifyAddress($event->address());
    }

    public function whenAddressWasVerified(AddressWasVerified $event): void
    {
        $this->application->grantAccess($event->memberAddress());
    }
}
