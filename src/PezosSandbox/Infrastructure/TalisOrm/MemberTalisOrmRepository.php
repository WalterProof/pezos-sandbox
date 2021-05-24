<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\TalisOrm;

use Assert\Assert;
use PezosSandbox\Domain\Model\Member\CouldNotFindMember;
use PezosSandbox\Domain\Model\Member\Member;
use PezosSandbox\Domain\Model\Member\MemberRepository;
use PezosSandbox\Domain\Model\Member\PubKey;
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

    public function getByPubKey(PubKey $pubKey): Member
    {
        try {
            $member = $this->aggregateRepository->getById(
                Member::class,
                $pubKey,
            );
            Assert::that($member)->isInstanceOf(Member::class);
            /* @var Member $member */

            return $member;
        } catch (AggregateNotFoundException $exception) {
            throw CouldNotFindMember::withPubKey($pubKey);
        }
    }

    public function exists(PubKey $pubKey): bool
    {
        try {
            $this->getByPubKey($pubKey);

            return true;
        } catch (CouldNotFindMember $exception) {
            return false;
        }
    }
}
