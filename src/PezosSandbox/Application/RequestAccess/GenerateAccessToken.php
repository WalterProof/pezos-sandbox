<?php

declare(strict_types=1);

namespace PezosSandbox\Application\RequestAccess;

use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Domain\Model\Member\AccessWasGrantedToMember;

final class GenerateAccessToken
{
    private ApplicationInterface $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    public function whenAccessWasGrantedToMember(
        AccessWasGrantedToMember $event
    ): void {
        $this->application->generateAccessToken($event->address());
    }
}
