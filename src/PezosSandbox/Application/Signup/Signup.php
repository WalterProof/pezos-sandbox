<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Signup;

final class Signup
{
    private string $address;
    private string $plainTextPassword;

    public function __construct(string $address, string $plainTextPassword)
    {
        $this->address           = $address;
        $this->plainTextPassword = $plainTextPassword;
    }

    public function address(): string
    {
        return $this->address;
    }

    public function plainTextPassword(): string
    {
        return $this->plainTextPassword;
    }
}
