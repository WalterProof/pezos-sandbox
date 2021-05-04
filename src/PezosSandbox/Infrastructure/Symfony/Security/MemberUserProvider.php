<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Security;

use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\Members\Member;
use PezosSandbox\Domain\Model\Member\CouldNotFindMember;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class MemberUserProvider implements UserProviderInterface
{
    private ApplicationInterface $application;

    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    public function loadUserByUsername(string $username): UserInterface
    {
        try {
            return $this->application->getOneMemberByAddress($username);
        } catch (CouldNotFindMember $exception) {
            throw new UsernameNotFoundException(
                'User not found',
                0,
                $exception,
            );
        }
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof Member) {
            throw new UnsupportedUserException(
                sprintf('Invalid user class "%s".', \get_class($user)),
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass(string $class): bool
    {
        return Member::class === $class;
    }
}
