<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

final class AccessPolicy
{
    private ApplicationInterface $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }
}
