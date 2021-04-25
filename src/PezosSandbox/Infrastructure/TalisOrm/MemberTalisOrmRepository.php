<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\TalisOrm;

use Assert\Assert;
use PezosSandbox\Domain\Model\Member\Address;
use PezosSandbox\Domain\Model\Member\CouldNotFindMember;
use PezosSandbox\Domain\Model\Member\Member;
use PezosSandbox\Domain\Model\Member\MemberRepository;
use TalisOrm\AggregateNotFoundException;
use TalisOrm\AggregateRepository;

final class MemberTalisOrmRepository implements MemberRepository
{
    private AggregateRepository $aggregateRepository;

    public function __construct(AggregateRepository $aggregateRepository)
    {
        $this->aggregateRepository = $aggregateRepository;
    }

    public function save(Member $member): void
    {
        $this->aggregateRepository->save($member);
    }

    public function getByAddress(Address $address): Member
    {
        try {
            $member = $this->aggregateRepository->getById(
                Member::class,
                $address,
            );
            Assert::that($member)->isInstanceOf(Member::class);
            /* @var Member $member */

            return $member;
        } catch (AggregateNotFoundException $exception) {
            throw CouldNotFindMember::withAddress($address);
        }
    }

    public function exists(Address $address): bool
    {
        try {
            $this->getByAddress($address);

            return true;
        } catch (CouldNotFindMember $exception) {
            return false;
        }
    }
}
