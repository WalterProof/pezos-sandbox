<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Domain\Model\Token\Address;

final class UpdateToken
{
    private string $address;
    private string $symbol;
    private string $name;
    private string $thumbnailUri;
    private int $decimals;
    private bool $active;

    public function __construct(
        string $address,
        string $symbol,
        string $name,
        string $thumbnailUri,
        int $decimals,
        bool $active
    ) {
        $this->address      = $address;
        $this->symbol       = $symbol;
        $this->name         = $name;
        $this->thumbnailUri = $thumbnailUri;
        $this->decimals     = $decimals;
        $this->active       = $active;
    }

    public function address(): Address
    {
        return Address::fromString($this->address);
    }

    public function symbol(): string
    {
        return $this->symbol;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function thumbnailUri(): string
    {
        return $this->thumbnailUri;
    }

    public function decimals(): int
    {
        return $this->decimals;
    }

    public function active(): bool
    {
        return $this->active;
    }
}
