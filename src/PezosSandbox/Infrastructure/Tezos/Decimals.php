<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Tezos;

use Assert\Assert;

final class Decimals
{
    private int $decimals;

    private function __construct(int $decimals)
    {
        Assert::that($decimals)->greaterOrEqualThan(
            0,
            'Decimals should be greater or equal than 0'
        );
        $this->decimals = $decimals;
    }

    public static function fromInt(int $int): self
    {
        return new self($int);
    }

    public function asInt(): int
    {
        return $this->decimals;
    }
}
