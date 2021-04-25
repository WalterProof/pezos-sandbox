<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Members;

final class MemberForAdministrator
{
    private string $memberId;

    private string $requestedAccessAt;

    private bool $accessWasGranted;

    public function __construct(
        string $memberId,
        string $requestedAccessAt,
        bool $accessWasGranted
    ) {
        $this->memberId          = $memberId;
        $this->requestedAccessAt = $requestedAccessAt;
        $this->accessWasGranted  = $accessWasGranted;
    }

    public function memberId(): string
    {
        return $this->memberId;
    }

    public function requestedAccessAt(): string
    {
        return $this->requestedAccessAt;
    }

    public function accessWasGranted(): bool
    {
        return $this->accessWasGranted;
    }
}
