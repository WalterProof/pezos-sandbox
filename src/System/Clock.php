<?php

declare(strict_types=1);

namespace App\System;

interface Clock
{
    public function currentTime(): \DateTimeImmutable;
}
