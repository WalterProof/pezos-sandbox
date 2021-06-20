<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Tezos\Storage;

class Storage
{
    private ?string $totalSupply = null;

    public function __construct(?string $totalSupply)
    {
        $this->totalSupply = $totalSupply;
    }

    public function totalSupply(): ?string
    {
        return $this->totalSupply;
    }
}
