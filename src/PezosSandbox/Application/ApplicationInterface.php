<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Application\Exchanges\Exchange;
use PezosSandbox\Application\Members\Member;
use PezosSandbox\Application\Tokens\Token;
use PezosSandbox\Domain\Model\Exchange\CouldNotFindExchange;
use PezosSandbox\Domain\Model\Member\CouldNotFindMember;
use PezosSandbox\Domain\Model\Member\PubKey;

interface ApplicationInterface
{
    public function grantAccess(PubKey $pubKey): void;

    /**
     * @throws CouldNotFindExchange
     */
    public function getOneExchangeByName(string $name): Exchange;

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

    /**
     * @return array<Exchange>
     */
    public function listExchanges(): array;

    public function addExchange(AddExchange $command): void;

    public function updateExchange(UpdateExchange $command): void;

    public function addTokenExchange(AddTokenExchange $command): void;

    public function updateTokenExchange(UpdateTokenExchange $command): void;

    public function removeTokenExchange(RemoveTokenExchange $command): void;

    public function addToken(AddToken $command): void;

    public function updateToken(UpdateToken $command): void;

    public function getCurrentTime(): \DateTimeImmutable;
}
