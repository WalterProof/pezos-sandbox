<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Member;

interface MemberRepository
{
    public function save(Member $member): void;

    /**
     * @throws CouldNotFindMember
     */
    public function getByPubKey(PubKey $pubKey): Member;

    public function exists(PubKey $pubKey): bool;
}
