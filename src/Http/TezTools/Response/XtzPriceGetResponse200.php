<?php

declare(strict_types=1);

namespace App\Http\TezTools\Response;

class XtzPriceGetResponse200
{
    public float $price;
    public float $price24h;
    public float $marketCap;
    public float $marketCap24h;
    public float $volume;
    public float $volume24h;
    public \DateTimeImmutable $updated;

    public function setUpdated(string $updated): void
    {
        $this->updated = new \DateTimeImmutable($updated);
    }
}
