<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

final class AddExchange
{
    private string $name;
    private string $homepage;

    public function __construct(string $name, string $homepage)
    {
        $this->name     = $name;
        $this->homepage = $homepage;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function homepage(): string
    {
        return $this->homepage;
    }
}
