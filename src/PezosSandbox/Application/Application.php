<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Application\Members\Member as MemberReadModel;
use PezosSandbox\Application\Members\Members;
use PezosSandbox\Domain\Model\Member\AccessToken;
use PezosSandbox\Domain\Model\Member\Address;
use PezosSandbox\Domain\Model\Member\Address as MemberAddress;
use PezosSandbox\Domain\Model\Member\MemberRepository;
use PezosSandbox\Domain\Service\AccessTokenGenerator;

class Application implements ApplicationInterface
{
    private MemberRepository $memberRepository;

    private EventDispatcher $eventDispatcher;

    private AccessTokenGenerator $accessTokenGenerator;

    private Members $members;

    public function __construct(
        MemberRepository $memberRepository,
        EventDispatcher $eventDispatcher,
        AccessTokenGenerator $accessTokenGenerator,
        Members $members
    ) {
        $this->memberRepository     = $memberRepository;
        $this->eventDispatcher      = $eventDispatcher;
        $this->accessTokenGenerator = $accessTokenGenerator;
        $this->members              = $members;
    }

    public function verifyAddress(Address $address): void
    {
        /* try { */
        /*     $purchase = $this->purchaseRepository->getByAddress($address); */
        /* } catch (CouldNotFindAddress $exception) { */
        /*     $this->eventDispatcher->dispatch( */
        /*         new ClaimWasDenied($address, 'invalid_purchase_id'), */
        /*     ); */

        /* return; */
        /* } */

        /* $purchase->claim(); */

        /* $this->purchaseRepository->save($purchase); */

        /* $this->eventDispatcher->dispatchAll($purchase->releaseEvents()); */
    }

    public function grantAccess(Address $address): void
    {
        $member = $this->memberRepository->getByAddress($address);

        $member->grantAccess();

        $this->memberRepository->save($member);

        $this->eventDispatcher->dispatchAll($member->releaseEvents());
    }

    /**
     * @param string|Address $memberAddress
     */
    public function generateAccessToken($memberAddress): void
    {
        if (!$memberAddress instanceof Address) {
            $memberAddress = Address::fromString($memberAddress);
        }

        $member = $this->memberRepository->getByAddress($memberAddress);

        $member->generateAccessToken($this->accessTokenGenerator);

        $this->memberRepository->save($member);

        $this->eventDispatcher->dispatchAll($member->releaseEvents());
    }

    public function clearAccessToken($memberAddress): void
    {
        if (!$memberAddress instanceof Address) {
            $memberAddress = Address::fromString($memberAddress);
        }

        $member = $this->memberRepository->getByAddress($memberAddress);

        $member->clearAccessToken();

        $this->memberRepository->save($member);

        $this->eventDispatcher->dispatchAll($member->releaseEvents());
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
