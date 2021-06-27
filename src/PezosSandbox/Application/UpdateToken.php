<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Domain\Model\Token\Address;
use PezosSandbox\Domain\Model\Token\TokenId;

final class UpdateToken
{
    private string $tokenId;
    private string $address;
    private array $metadata;
    private bool $active;
    private ?int $position;

    public function __construct(
        string $tokenId,
        string $address,
        array $metadata,
        bool $active,
        ?int $position
    ) {
        $this->tokenId  = $tokenId;
        $this->address  = $address;
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

    public function position(): ?int
    {
        return $this->position;
    }

    public function active(): bool
    {
        return $this->active;
    }
}
