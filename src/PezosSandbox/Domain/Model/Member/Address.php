<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Member;

use Assert\Assert;
use TalisOrm\AggregateId;

final class Address implements AggregateId
{
    private string $address;

    private function __construct(string $address)
    {
        Assert::that($address)->regex('/^[A-Za-z0-9_\-]{22}$/');
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
