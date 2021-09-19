<?php

declare(strict_types=1);

namespace App\Http\TezTools\Model;

class ContractLive
{
    public string $identifier;
    public string $tokenAddress;
    public int $tokenId;
    public float $tezPool;
    public float $tokenPool;
    public float $currentPrice;
    public float $lastPrice;
    public float $buyPrice;
    public float $sellPrice;
    public string $timestamp;
    public string $block;
}
