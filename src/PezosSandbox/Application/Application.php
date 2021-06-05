<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Application\Members\Member as MemberReadModel;
use PezosSandbox\Application\Members\Members;
use PezosSandbox\Application\RequestAccess\RequestAccess;
use PezosSandbox\Application\Tokens\Token as TokenReadModel;
use PezosSandbox\Application\Tokens\Tokens;
use PezosSandbox\Application\Tokens\TokenWasAlreadyAdded;
use PezosSandbox\Domain\Model\Member\Member;
use PezosSandbox\Domain\Model\Member\MemberRepository;
use PezosSandbox\Domain\Model\Member\PubKey;
use PezosSandbox\Domain\Model\Token\Address as TokenAddress;
use PezosSandbox\Domain\Model\Token\CouldNotFindToken;
use PezosSandbox\Domain\Model\Token\Token;
use PezosSandbox\Domain\Model\Token\TokenRepository;

class Application implements ApplicationInterface
{
    private MemberRepository $memberRepository;
    private TokenRepository $tokenRepository;
    private EventDispatcher $eventDispatcher;
    private Members $members;
    private Tokens $tokens;
    private Clock $clock;

    public function __construct(
        MemberRepository $memberRepository,
        TokenRepository $tokenRepository,
        EventDispatcher $eventDispatcher,
        Members $members,
        Tokens $tokens,
        Clock $clock
    ) {
        $this->memberRepository = $memberRepository;
        $this->tokenRepository  = $tokenRepository;
        $this->eventDispatcher  = $eventDispatcher;
        $this->members          = $members;
        $this->tokens           = $tokens;
        $this->clock            = $clock;
    }

    public function requestAccess(RequestAccess $command): void
    {
        $member = Member::requestAccess($command->pubKey(), $this->clock->currentTime());

        $this->memberRepository->save($member);
    }

    public function grantAccess(PubKey $pubKey): void
    {
        $member = $this->memberRepository->getByPubKey($pubKey);
        $member->grantAccess();
        $this->memberRepository->save($member);
    }

    public function addToken(AddToken $command): void
    {
        try {
            $this->tokenRepository->getByAddress($command->address());

            throw new TokenWasAlreadyAdded($command->address());
        } catch (CouldNotFindToken $exception) {
            $token = Token::createToken(
                $command->address(),
                $command->addressQuipuswap(),
                $command->kind(),
                $command->decimals(),
                $command->symbol(),
                $command->name(),
                $command->description(),
                $command->homepage(),
                $command->social(),
                $command->thumbnailUri(),
                $command->active()
            );

            $this->tokenRepository->save($token);
        }
    }

    public function toggleToken(string $address): void
    {
        $token = $this->tokenRepository->getByAddress(TokenAddress::fromString($address));
        $token->toggleActive();
        $this->tokenRepository->save($token);
    }

    public function updateToken(UpdateToken $command): void
    {
        $token = $this->tokenRepository->getByAddress($command->address());
        $token->update(
                $command->addressQuipuswap(),
                $command->kind(),
                $command->decimals(),
                $command->supplyAdjustment(),
                $command->symbol(),
                $command->name(),
                $command->description(),
                $command->homepage(),
                $command->social(),
                $command->thumbnailUri(),
                $command->active()
        );
        $this->tokenRepository->save($token);
    }

    public function getOneMemberByPubKey(string $pubKey): MemberReadModel
    {
        return $this->members->getOneByPubKey(
            PubKey::fromString($pubKey),
        );
    }

    public function getOneTokenByAddress(string $address): TokenReadModel
    {
        return $this->tokens->getOneByAddress(
            TokenAddress::fromString($address),
        );
    }

    public function listMembersForAdministrator(): array
    {
        return $this->members->listMembers();
    }

    /**
     * @return array<TokenReadModel>
     */
    public function listTokens(): array
    {
        return $this->tokens->listTokens();
    }

    /**
     * @return array<TokenReadModel>
     */
    public function listTokensForAdmin(): array
    {
        return $this->tokens->listTokensForAdmin();
    }
}
