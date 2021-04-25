<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Member;

use PezosSandbox\Domain\Model\Common\AbstractUserFacingError;

final class CouldNotFindMember extends AbstractUserFacingError
{
    public static function withAddress(Address $address): self
    {
        return new self('address.does_not_exist', [
            '{address}' => $address->asString(),
        ]);
    }

    public static function withAccessToken(AccessToken $accessToken): self
    {
        return new self(
            sprintf(
                'Could not find a member with access token %s',
                $accessToken->asString(),
            ),
        );
    }
}
