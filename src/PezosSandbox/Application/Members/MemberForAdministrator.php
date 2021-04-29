<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Members;

final class MemberForAdministrator
{
    private string $memberAddress;

    private string $requestedAccessAt;

    private bool $accessWasGranted;

    public function __construct(
        string $memberAddress,
        string $requestedAccessAt,
        bool $accessWasGranted
    ) {
        $this->memberAddress     = $memberAddress;
        $this->requestedAccessAt = $requestedAccessAt;
        $this->accessWasGranted  = $accessWasGranted;
    }

    public function memberAddress(): string
    {
        return $this->memberAddress;
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
