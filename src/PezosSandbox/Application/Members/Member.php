<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Members;

use PezosSandbox\Domain\Model\Member\Address;
use Symfony\Component\Security\Core\User\UserInterface;

final class Member implements UserInterface
{
    private string $address;
    private string $password;

    public function __construct(string $address, string $password)
    {
        $this->address = $address;
        $this->password = $password;
    }

    /**
     * @return array<string>
     */
    public function getRoles(): array
    {
        return ['ROLE_MEMBER'];
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->address;
    }

    public function address(): Address
    {
        return Address::fromString($this->address);
    }

    public function eraseCredentials(): void
    {
    }
}
