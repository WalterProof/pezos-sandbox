<?php
declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Member;

use PezosSandbox\Domain\Model\Common\AbstractUserFacingError;

final class CouldNotFindMember extends AbstractUserFacingError
{
    public static function withAddress(Address $memberAddress): self
    {
        return new self(
            'address.does_not_exist',
            [
                '{address}' => $memberAddress->asString()
            ]
        );
    }
}
