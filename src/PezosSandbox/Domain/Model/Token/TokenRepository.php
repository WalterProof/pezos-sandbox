<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Token;

interface TokenRepository
{
    public function save(Token $token): void;

    /**
     * @throws CouldNotFindToken
     */
    public function getById(TokenId $tokenId): Token;

    public function exists(TokenId $tokenId): bool;

    public function nextIdentity(): TokenId;
}
