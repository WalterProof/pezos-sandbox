<?php

declare(strict_types=1);

namespace PezosSandbox\Application\RequestAccess;

final class RequestAccess
{
    private string $payload;
    private string $publicKey;
    private string $signature;

    public function __construct(string $payload, $publicKey, $signature)
    {
        $this->payload   = $payload;
        $this->publicKey = $publicKey;
        $this->signature = $signature;
    }

    public function payload(): string
    {
        return $this->payload;
    }

    public function publicKey(): string
    {
        return $this->publicKey;
    }

    public function signature(): string
    {
        return $this->signature;
    }
}
