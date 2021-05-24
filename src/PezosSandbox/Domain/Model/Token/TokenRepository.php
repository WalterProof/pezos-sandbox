<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Token;

interface TokenRepository
{
    public function save(Token $token): void;

    /**
     * @throws CouldNotFindToken
     */
    public function getByAddress(Address $address): Token;

    public function exists(Address $address): bool;
}
