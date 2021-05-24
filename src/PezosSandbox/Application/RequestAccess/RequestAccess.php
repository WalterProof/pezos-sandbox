<?php

declare(strict_types=1);

namespace PezosSandbox\Application\RequestAccess;

use PezosSandbox\Domain\Model\Member\PubKey;

final class RequestAccess
{
    private string $pubKey;

    public function __construct(string $pubKey)
    {
        $this->pubKey = $pubKey;
    }

    public function pubKey(): PubKey
    {
        return PubKey::fromString($this->pubKey);
    }
}
