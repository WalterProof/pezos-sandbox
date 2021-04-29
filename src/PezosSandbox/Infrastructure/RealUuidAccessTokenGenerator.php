<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure;

use PezosSandbox\Domain\Model\Member\AccessToken;
use PezosSandbox\Domain\Service\AccessTokenGenerator;
use Ramsey\Uuid\Uuid;

final class RealUuidAccessTokenGenerator implements AccessTokenGenerator
{
    public function generate(): AccessToken
    {
        return AccessToken::fromString(Uuid::uuid4()->toString());
    }
}
