<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Tag;

use PezosSandbox\Domain\Model\Common\Uuid;
use TalisOrm\AggregateId;

final class TagId implements AggregateId
{
    use Uuid;

    public function __toString(): string
    {
        return $this->asString();
    }
}
