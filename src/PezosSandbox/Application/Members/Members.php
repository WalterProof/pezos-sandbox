<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Members;

use PezosSandbox\Domain\Model\Member\CouldNotFindMember;
use PezosSandbox\Domain\Model\Member\PubKey;

interface Members
{
    /**
     * @throws CouldNotFindMember
     */
    public function getOneByPubKey(PubKey $pubKey): Member;

    /**
     * @return array<MemberForAdministrator>
     */
    public function listMembers(): array;
}
