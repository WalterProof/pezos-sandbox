<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Member;

final class AnAccessTokenWasGenerated
{
    private Address $address;

    private AccessToken $accessToken;

    public function __construct(Address $address, AccessToken $accessToken)
    {
        $this->address     = $address;
        $this->accessToken = $accessToken;
    }

    public function address(): Address
    {
        return $this->address;
    }

    public function accessToken(): AccessToken
    {
        return $this->accessToken;
    }
}
