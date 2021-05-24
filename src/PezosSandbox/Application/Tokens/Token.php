<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Tokens;

final class Token
{
    private string $address;
    private string $kind;
    private string $symbol;
    private string $name;
    private int $decimals;
    private string $addressQuipuswap;
    private string $thumbnailUri;
    private bool $active;

    public function __construct(
        string $address,
        string $kind,
        string $symbol,
        string $name,
        int $decimals,
        string $addressQuipuswap,
        string $thumbnailUri,
        bool $active
    ) {
        $this->address          = $address;
        $this->kind             = $kind;
        $this->symbol           = $symbol;
        $this->name             = $name;
        $this->decimals         = $decimals;
        $this->addressQuipuswap = $addressQuipuswap;
        $this->thumbnailUri     = $thumbnailUri;
        $this->active           = $active;
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
}
