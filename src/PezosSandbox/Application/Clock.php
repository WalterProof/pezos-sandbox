<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use DateTimeImmutable;

interface Clock
{
    public function currentTime(): DateTimeImmutable;
}
