<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Tokens;

use PezosSandbox\Domain\Model\Token\Address;

interface Tokens
{
    /**
     * @throws CouldNotFindToken
     */
    public function getOneByAddress(Address $address): Token;

    /**
     * @return array<Token>
     */
    public function listTokens(): array;

    /**
     * @return array<Token>
     */
    public function listTokensForAdmin(): array;
}
