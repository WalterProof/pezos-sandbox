<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Service;

use PezosSandbox\Domain\Model\Member\AccessToken;

interface AccessTokenGenerator
{
    public function generate(): AccessToken;
}
