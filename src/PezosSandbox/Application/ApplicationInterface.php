<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Application\Members\Member;
use PezosSandbox\Domain\Model\Member\CouldNotFindMember;
use PezosSandbox\Application\Register\Register;

interface ApplicationInterface
{
    public function register(Register $command): void;

    /**
     * @throws CouldNotFindMember
     */
    public function getOneMemberByAddress(string $address): Member;
}
