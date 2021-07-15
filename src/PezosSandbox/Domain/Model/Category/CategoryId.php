<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Category;

use PezosSandbox\Domain\Model\Common\Uuid;
use TalisOrm\AggregateId;

final class CategoryId implements AggregateId
{
    use Uuid;

    public function __toString(): string
    {
        return $this->asString();
    }
}
