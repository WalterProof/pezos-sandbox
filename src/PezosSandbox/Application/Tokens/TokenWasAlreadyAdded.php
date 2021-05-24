<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Tokens;

use PezosSandbox\Domain\Model\Token\Address;

final class TokenWasAlreadyAdded extends \RuntimeException
{
    public function __construct(Address $address)
    {
        parent::__construct(
            sprintf(
                'Could not add token with address %s because it was already added',
                $address->asString(),
            ),
        );
    }
}
