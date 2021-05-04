<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Application\Members\Member as MemberReadModel;
use PezosSandbox\Application\Members\Members;
use PezosSandbox\Application\Register\Register;
use PezosSandbox\Domain\Model\Member\Address as MemberAddress;
use PezosSandbox\Domain\Model\Member\Member;
use PezosSandbox\Domain\Model\Member\MemberRepository;

class Application implements ApplicationInterface
{
    private MemberRepository $memberRepository;
    private EventDispatcher $eventDispatcher;
    private Members $members;
    private Clock $clock;

    public function __construct(
        MemberRepository $memberRepository,
        EventDispatcher $eventDispatcher,
        Members $members,
        Clock $clock
    ) {
        $this->memberRepository     = $memberRepository;
        $this->eventDispatcher      = $eventDispatcher;
        $this->members              = $members;
        $this->clock                = $clock;
    }

    public function register(Register $command): void
    {
        if ($this->memberRepository->exists($command->address())) {
            throw AlreadyRegistered::pubKey($command->pubKey());
        }

        $member = Member::register(
            $command->pubKey(),
            $command->password,
            $this->clock->currentTime()
        );

        $this->memberRepository->save($member);

        $this->eventDispatcher->dispatchAll($member->releaseEvents());
    }

    public function getOneMemberByAddress(string $address): MemberReadModel
    {
        return $this->members->getOneByAddress(
            MemberAddress::fromString($address),
        );
    }

    public function listMembersForAdministrator(): array
    {
        return $this->members->listMembers();
    }
}
