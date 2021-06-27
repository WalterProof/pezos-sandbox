<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Tokens;

use PezosSandbox\Domain\Model\Token\Address;
use PezosSandbox\Domain\Model\Token\TokenId;

final class Token
{
    private string $tokenId;
    private string $address;
    private array $metadata;
    private bool $active;
    private ?int $position;

    private ?array $exchanges = null;
    private ?array $tags      = null;

    public function __construct(
        TokenId $tokenId,
        Address $address,
        array $metadata,
        bool $active,
        ?int $position
    ) {
        $this->tokenId  = $tokenId->asString();
        $this->address  = $address->asString();
        $this->metadata = $metadata;
        $this->active   = $active;
        $this->position = $position;
    }

    public function tokenId(): TokenId
    {
        return TokenId::fromString($this->tokenId);
    }

    public function address(): Address
    {
        return Address::fromString($this->address);
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function position(): ?int
    {
        return $this->position;
    }

    public function withExchanges(array $exchanges): self
    {
        $copy = clone $this;

        $copy->exchanges = $exchanges;

        return $copy;
    }

    public function exchanges(): ?array
    {
        return $this->exchanges;
    }

    public function withTags(array $tags): self
    {
        $copy = clone $this;

        $copy->tags = $tags;

        return $copy;
    }

    public function tags(): ?array
    {
        return $this->tags;
    }
}
