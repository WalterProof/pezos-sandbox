<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Token;

final class TokenWasUpdated
{
    private TokenId $tokenId;
    private Address $address;
    private array $metadata;
    private bool $active;

    public function __construct(
        TokenId $tokenId,
        Address $address,
        array $metadata,
        bool $active
    ) {
        $this->tokenId  = $tokenId;
        $this->address  = $address;
        $this->metadata = $metadata;
        $this->active   = $active;
    }

    public function tokenId(): TokenId
    {
        return $this->tokenId;
    }

    public function address(): Address
    {
        return $this->address;
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function active(): bool
    {
        return $this->active;
    }
}
