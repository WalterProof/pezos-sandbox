<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Member;

interface MemberRepository
{
    public function save(Member $member): void;

    /**
     * @throws CouldNotFindMember
     */
    public function getByAddress(Address $address): Member;

    public function exists(Address $address): bool;
}
