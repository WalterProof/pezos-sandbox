<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Members;

use PezosSandbox\Domain\Model\Member\PubKey;
use Symfony\Component\Security\Core\User\UserInterface;

final class Member implements UserInterface
{
    private string $pubKey;
    private string $address;

    public function __construct(string $pubKey, string $address)
    {
        $this->pubKey  = $pubKey;
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
        return $this->pubKey;
    }

    public function pubKey(): PubKey
    {
        return PubKey::fromString($this->pubKey);
    }

    public function address(): string
    {
        return $this->address;
    }

    public function eraseCredentials(): void
    {
    }
}
