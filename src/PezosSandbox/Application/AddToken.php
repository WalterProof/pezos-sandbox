<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Domain\Model\Token\Address;

final class AddToken
{
    private string $address;
    private array $metadata;
    private bool $active;
    private array $exchanges;

    public function __construct(
        string $address,
        array $metadata,
        bool $active,
        array $exchanges
    ) {
        $this->address   = $address;
        $this->metadata  = $metadata;
        $this->active    = $active;
        $this->exchanges = $exchanges;
    }

    public function address(): Address
    {
        return Address::fromString($this->address);
    }

    public function metadata(): array
    {
        return $this->metadata;
    }

    public function exchanges(): array
    {
        return $this->exchanges;
    }

    public function active(): bool
    {
        return $this->active;
    }
}
