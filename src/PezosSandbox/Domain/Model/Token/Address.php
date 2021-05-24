<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Token;

use TalisOrm\AggregateId;

final class Address implements AggregateId
{
    private string $address;

    private function __construct(string $address)
    {
        $this->address = $address;
    }

    public function __toString(): string
    {
        return $this->asString();
    }

    public static function fromString(string $string): self
    {
        return new self($string);
    }

    public function asString(): string
    {
        return $this->address;
    }
}
