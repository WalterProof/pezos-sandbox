<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

use PezosSandbox\Application\Members\Member;
use PezosSandbox\Application\Signup\Signup;
use PezosSandbox\Domain\Model\Member\CouldNotFindMember;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

interface ApplicationInterface
{
    public function signup(
        Signup $command,
        UserPasswordEncoderInterface $passwordEncoder
    ): void;

    /**
     * @throws CouldNotFindMember
     */
    public function getOneMemberByAddress(string $address): Member;
}
