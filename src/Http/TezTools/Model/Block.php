<?php

declare(strict_types=1);

namespace App\Http\TezTools\Model;

class Block
{
    public string $hash;
    public string $timestamp;
    public string $found;
    public string $chainId;
    public string $baker;
    public array $operations;
}
