<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Member;

use PezosSandbox\Domain\Model\Common\AbstractUserFacingError;

final class CouldNotGrantAccess extends AbstractUserFacingError
{
    public static function becauseSignatureIsInvalid(Address $address): self
    {
        return new self('could_not_grant_access.because_signature_is_invalid', [
            'address' => $address->asString(),
        ]);
    }
}
