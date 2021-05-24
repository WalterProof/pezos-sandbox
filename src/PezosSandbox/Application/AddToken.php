<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Domain\Model\Token\Address;

final class AddToken
{
    private string $address;
    private string $kind;
    private string $symbol;
    private string $name;
    private int $decimals;
    private string $addressQuipuswap;

    public function __construct(
        string $address,
        string $kind,
        string $symbol,
        string $name,
        int $decimals,
        string $addressQuipuswap
    ) {
        $this->address          = $address;
        $this->kind             = $kind;
        $this->symbol           = $symbol;
        $this->name             = $name;
        $this->decimals         = $decimals;
        $this->addressQuipuswap = $addressQuipuswap;
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
}
