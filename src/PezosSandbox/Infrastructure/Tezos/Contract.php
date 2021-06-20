<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Tezos;

use Assert\Assert;

final class Contract
{
    private string $contract;

    private function __construct(string $contract)
    {
        Assert::that($contract)->notEmpty('Contract should not be empty');
        $this->contract = $contract;
    }

    public static function fromString(string $string): self
    {
        return new self($string);
    }

    public function asString(): string
    {
        return $this->contract;
    }
}
