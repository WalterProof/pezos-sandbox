<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Members;

use PezosSandbox\Domain\Model\Member\Address;
use Symfony\Component\Security\Core\User\UserInterface;

final class Member implements UserInterface
{
    private string $address;

    public function __construct(string $address)
    {
        $this->address = $address;
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
        return null;
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
