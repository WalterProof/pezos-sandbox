<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Token;

use PezosSandbox\Domain\Model\Common\Uuid;
use TalisOrm\AggregateId;

final class TokenId implements AggregateId
{
    use Uuid;

    public function __toString(): string
    {
        return $this->asString();
    }
}