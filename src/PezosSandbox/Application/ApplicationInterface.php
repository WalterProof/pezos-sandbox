<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Application\Members\Member;
use PezosSandbox\Application\RequestAccess\RequestAccess;
use PezosSandbox\Application\Tokens\Token;
use PezosSandbox\Domain\Model\Member\CouldNotFindMember;
use PezosSandbox\Domain\Model\Member\PubKey;

interface ApplicationInterface
{
    public function requestAccess(RequestAccess $command): void;

    public function grantAccess(PubKey $pubKey): void;

    /**
     * @throws CouldNotFindMember
     */
    public function getOneMemberByPubKey(string $pubKey): Member;

    /**
     * @throws CouldNotFindToken
     */
    public function getOneTokenByAddress(string $address): Token;

    /**
     * @return array<Token>
     */
    public function listTokens(): array;

    /**
     * @return array<Token>
     */
    public function listTokensForAdmin(): array;

    public function addToken(AddToken $command): void;

    public function updateToken(UpdateToken $command): void;
}
