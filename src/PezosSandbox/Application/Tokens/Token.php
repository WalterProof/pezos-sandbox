<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Tokens;

final class Token
{
    private string $address;
    private string $addressQuipuswap;
    private string $kind;
    private int $decimals;
    private string $symbol;
    private string $name;
    private string $description;
    private string $homepage;
    private string $thumbnailUri;
    private bool $active;
    private array $social;

    public function __construct(
        string $address,
        string $addressQuipuswap,
        string $kind,
        int $decimals,
        string $symbol,
        string $name,
        string $description,
        string $homepage,
        string $thumbnailUri,
        bool $active,
        array $social
    ) {
        $this->address          = $address;
        $this->addressQuipuswap = $addressQuipuswap;
        $this->kind             = $kind;
        $this->decimals         = $decimals;
        $this->symbol           = $symbol;
        $this->name             = $name;
        $this->description      = $description;
        $this->homepage         = $homepage;
        $this->thumbnailUri     = $thumbnailUri;
        $this->active           = $active;
        $this->social           = $social;
    }

    public function address(): string
    {
        return $this->address;
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

    public function thumbnailUri(): string
    {
        return $this->thumbnailUri;
    }

    public function active(): bool
    {
        return $this->active;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function homepage(): string
    {
        return $this->homepage;
    }

    public function social(): array
    {
        return $this->social;
    }
}
