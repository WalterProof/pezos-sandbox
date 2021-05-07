<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Application\Members\Member as MemberReadModel;
use PezosSandbox\Application\Members\Members;
use PezosSandbox\Application\Signup\Signup;
use PezosSandbox\Domain\Model\Member\Address as MemberAddress;
use PezosSandbox\Domain\Model\Member\CouldNotFindMember;
use PezosSandbox\Domain\Model\Member\Member;
use PezosSandbox\Domain\Model\Member\MemberRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;

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
        $this->memberRepository = $memberRepository;
        $this->eventDispatcher = $eventDispatcher;
        $this->members = $members;
        $this->clock = $clock;
    }

    public function signup(
        Signup $command,
        UserPasswordEncoderInterface $passwordEncoder
    ): void {
        $address = MemberAddress::fromString($command->address());
        try {
            $memberRead = $this->members->getOneByAddress($address);
            $encodedPassword = $passwordEncoder->encodePassword(
                $memberRead,
                $command->plainTextPassword(),
            );
            $member = $this->memberRepository->getByAddress($address);
            $member->grantAccess($encodedPassword);
        } catch (CouldNotFindMember $exception) {
            $encodedPassword = $passwordEncoder->encodePassword(
                new MemberReadModel(
                    $command->address(),
                    $command->plainTextPassword(),
                ),
                $command->plainTextPassword(),
            );
            $member = Member::register(
                $address,
                $encodedPassword,
                $this->clock->currentTime(),
            );
        }

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
