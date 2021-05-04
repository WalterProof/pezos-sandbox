<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Members;

use PezosSandbox\Domain\Model\Member\AccessToken;
use PezosSandbox\Domain\Model\Member\Address;
use PezosSandbox\Domain\Model\Member\CouldNotFindMember;

interface Members
{
    /**
     * @throws CouldNotFindMember
     */
    public function getOneByAddress(Address $address): Member;

    /**
     * @return array<MemberForAdministrator>
     */
    public function listMembers(): array;
}
