<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use Bzzhh\Pezos\Keys\Ed25519;
use Bzzhh\Pezos\Keys\PubKey;
use PezosSandbox\Application\Members\Member as MemberReadModel;
use PezosSandbox\Application\Members\Members;
use PezosSandbox\Application\RequestAccess\RequestAccess;
use PezosSandbox\Domain\Model\Member\AccessToken;
use PezosSandbox\Domain\Model\Member\Address;
use PezosSandbox\Domain\Model\Member\Address as MemberAddress;
use PezosSandbox\Domain\Model\Member\CouldNotGrantAccess;
use PezosSandbox\Domain\Model\Member\Member;
use PezosSandbox\Domain\Model\Member\MemberRepository;
use PezosSandbox\Domain\Service\AccessTokenGenerator;

class Application implements ApplicationInterface
{
    private MemberRepository $memberRepository;

    private EventDispatcher $eventDispatcher;

    private AccessTokenGenerator $accessTokenGenerator;

    private Members $members;

    private Clock $clock;

    public function __construct(
        MemberRepository $memberRepository,
        EventDispatcher $eventDispatcher,
        AccessTokenGenerator $accessTokenGenerator,
        Members $members,
        Clock $clock
    ) {
        $this->memberRepository     = $memberRepository;
        $this->eventDispatcher      = $eventDispatcher;
        $this->accessTokenGenerator = $accessTokenGenerator;
        $this->members              = $members;
        $this->clock                = $clock;
    }

    public function requestAccess(RequestAccess $command): void
    {
        $pubKey  = PubKey::fromBase58($command->publicKey(), new Ed25519());
        $address = Address::fromString($pubKey->getAddress());

        if (
            !$pubKey->verifySignedHex(
                $command->signature(),
                $command->payload(),
            )
        ) {
            throw CouldNotGrantAccess::becauseSignatureIsInvalid($address);

            return;
        }

        if (!$this->memberRepository->exists($address)) {
            $member = Member::requestAccess(
                $address,
                $this->clock->currentTime(),
            );

            $member->grantAccess();
            $member->generateAccessToken($this->accessTokenGenerator);
        }

        $this->memberRepository->save($member);
    }

    public function getOneMemberByAccessToken(
        string $accessToken
    ): MemberReadModel {
        return $this->members->getOneByAccessToken(
            AccessToken::fromString($accessToken),
        );
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
