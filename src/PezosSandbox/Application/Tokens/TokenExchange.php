<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Tokens;

final class TokenExchange
{
    private string $name;
    private string $contract;

    public function __construct(string $name, string $contract)
    {
        $this->name     = $name;
        $this->contract = $contract;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function contract(): string
    {
        return $this->contract;
    }
}
