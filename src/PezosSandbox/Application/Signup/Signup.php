<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Register;

final class Signup
{
    private string $password;
    private string $pubKey;
    private string $signature;

    public function __construct(
        string $password,
        string $pubKey,
        string $signature
    ) {
        $this->password = $password;
        $this->pubKey = $pubKey;
        $this->signature = $signature;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function pubKey(): string
    {
        return $this->pubKey;
    }

    public function signature(): string
    {
        return $this->signature;
    }
}
