<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Member;

use PezosSandbox\Domain\Model\Common\AbstractUserFacingError;

final class CouldNotFindMember extends AbstractUserFacingError
{
    public static function withPubKey(PubKey $pubKey): self
    {
        return new self('pub_key.does_not_exist', [
            '{pub_key}' => $pubKey->asString(),
        ]);
    }
}
