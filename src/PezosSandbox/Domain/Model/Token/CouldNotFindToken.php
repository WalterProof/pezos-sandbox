<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Token;

use PezosSandbox\Domain\Model\Common\AbstractUserFacingError;

final class CouldNotFindToken extends AbstractUserFacingError
{
    public static function withId(TokenId $tokenId): self
    {
        return new self('id.does_not_exist', [
            '{tokenId}' => $tokenId->asString(),
        ]);
    }

    public static function withAddress(Address $tokenAddress): self
    {
        return new self('address.does_not_exist', [
            '{address}' => $tokenAddress->asString(),
        ]);
    }
}
