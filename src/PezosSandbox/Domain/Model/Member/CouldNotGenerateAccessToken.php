<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Member;

use PezosSandbox\Domain\Model\Common\AbstractUserFacingError;

final class CouldNotGenerateAccessToken extends AbstractUserFacingError
{
    public static function becauseMemberHasNotBeenGrantedAccessYet(
        Address $address
    ): self {
        return new self(
            'could_not_generate_access_token.because_member_has_not_been_granted_access_yet',
            ['address' => $address->asString()],
        );
    }
}
