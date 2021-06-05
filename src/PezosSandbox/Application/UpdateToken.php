<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Domain\Model\Token\Address;

final class UpdateToken
{
    private string $address;
    private string $addressQuipuswap;
    private string $kind;
    private int $decimals;
    private ?int $supplyAdjustment;
    private string $symbol;
    private string $name;
    private ?string $description;
    private ?string $homepage;
    private ?string $thumbnailUri;
    private bool $active;

    public function __construct(
        string $address,
        string $addressQuipuswap,
        string $kind,
        int $decimals,
        ?int $supplyAdjustment,
        string $symbol,
        string $name,
        ?string $description = null,
        ?string $homepage = null,
        ?array $social = null,
        ?string $thumbnailUri = null,
        bool $active = true
    ) {
        $this->address          = $address;
        $this->addressQuipuswap = $addressQuipuswap;
        $this->kind             = $kind;
        $this->decimals         = $decimals;
        $this->supplyAdjustment = $supplyAdjustment;
        $this->symbol           = $symbol;
        $this->name             = $name;
        $this->description      = $description;
        $this->homepage         = $homepage;
        $this->social           = $social;
        $this->thumbnailUri     = $thumbnailUri;
        $this->active           = $active;
    }

    public function address(): Address
    {
        return Address::fromString($this->address);
    }

    public function kind(): string
    {
        return $this->kind;
    }

    public function symbol(): string
    {
        return $this->symbol;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function decimals(): int
    {
        return $this->decimals;
    }

    public function addressQuipuswap(): string
    {
        return $this->addressQuipuswap;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function homepage(): ?string
    {
        return $this->homepage;
    }

    public function thumbnailUri(): ?string
    {
        return $this->thumbnailUri;
    }

    public function active(): bool
    {
        return $this->active;
    }

    public function social(): ?array
    {
        return $this->social;
    }

    public function supplyAdjustment(): ?int
    {
        return $this->supplyAdjustment;
    }
}
