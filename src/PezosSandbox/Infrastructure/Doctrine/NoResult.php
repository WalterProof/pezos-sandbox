<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Doctrine;

use RuntimeException;
use function Safe\json_encode;

final class NoResult extends RuntimeException
{
    /**
     * @param array<int|string,mixed> $parameters
     */
    public static function forQuery(string $query, array $parameters): self
    {
        return new self(
            sprintf(
                'Query "%s" (parameters: %s) produced no results',
                $query,
                json_encode($parameters),
            ),
        );
    }
}
