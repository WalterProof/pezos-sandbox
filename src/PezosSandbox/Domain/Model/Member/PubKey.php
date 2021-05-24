<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Member;

use TalisOrm\AggregateId;

final class PubKey implements AggregateId
{
    private string $pubKey;

    private function __construct(string $pubKey)
    {
        $this->pubKey = $pubKey;
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
        return $this->pubKey;
    }
}
