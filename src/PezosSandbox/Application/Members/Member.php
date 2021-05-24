<?php

declare(strict_types=1);

namespace PezosSandbox\Application\Members;

use PezosSandbox\Domain\Model\Member\PubKey;
use Symfony\Component\Security\Core\User\UserInterface;

final class Member implements UserInterface
{
    private string $pubKey;

    public function __construct(string $pubKey)
    {
        $this->pubKey = $pubKey;
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

    public function eraseCredentials(): void
    {
    }
}
