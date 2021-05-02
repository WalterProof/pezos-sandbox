<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure;

use DateTimeImmutable;
use DateTimeZone;
use PezosSandbox\Application\Clock;

final class SystemClock implements Clock
{
    public function currentTime(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}
