<?php

declare(strict_types=1);

namespace App\Http\TezTools\Model;

class Contract
{
    public string $identifier;
    public string $symbol;
    public string $tokenAddress;
    public int $tokenId;
    public string $name;
    public string $type;
    public string $address;
    public string $thumbnailUri;
    public string $tags;
}
