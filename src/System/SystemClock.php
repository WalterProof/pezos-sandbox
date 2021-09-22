<?php

declare(strict_types=1);

namespace App\System;

class SystemClock implements Clock
{
    public function currentTime(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }
}
