<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Token;

use InvalidArgumentException;
use TalisOrm\AggregateId;

final class Address implements AggregateId
{
    private string $contract;
    private ?int $id;

    private function __construct(string $contract, ?int $id = null)
    {
        $this->contract = $contract;
        $this->id       = $id;
    }

    public function __toString(): string
    {
        return $this->asString();
    }

    public function contract(): string
    {
        return $this->contract;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public static function fromString(string $string): self
    {
        if (!preg_match('/^(KT1\w{33})_?(\d+)?/', $string, $matches)) {
            throw new InvalidArgumentException('The address is invalid');
        }

        return new self(
            $matches[1],
            isset($matches[2]) ? \intval($matches[2]) : null
        );
    }

    public static function fromState(string $contract, ?int $id = null)
    {
        return new self($contract, $id);
    }

    public function asString(): string
    {
        return $this->contract.(!\is_null($this->id) ? '_'.$this->id : '');
    }
}
