<?php

declare(strict_types=1);

namespace PezosSandbox\Application\RequestAccess;

use PezosSandbox\Domain\Model\Member\PubKey;

final class RequestAccess
{
    private string $pubKey;
    private string $address;

    public function __construct(string $pubKey, string $address)
    {
        $this->pubKey  = $pubKey;
        $this->address = $address;
    }

    public function pubKey(): PubKey
    {
        return PubKey::fromString($this->pubKey);
    }

    public function address(): string
    {
        return $this->address;
    }
}
