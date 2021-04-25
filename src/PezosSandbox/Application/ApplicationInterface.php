<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Application\Members\Member;
use PezosSandbox\Domain\Model\Member\Address;
use PezosSandbox\Domain\Model\Member\CouldNotFindMember;

interface ApplicationInterface
{
    public function verifyAddress(Address $address): void;

    public function grantAccess(Address $address): void;

    public function generateAccessToken(Address $address): void;

    /**
     * @throws CouldNotFindMember
     */
    public function getOneMemberByAccessToken(string $accessToken): Member;

    /**
     * @throws CouldNotFindMember
     */
    public function getOneMemberByAddress(string $address): Member;
}
