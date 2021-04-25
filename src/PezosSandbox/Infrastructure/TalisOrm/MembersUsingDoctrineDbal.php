<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\TalisOrm;

use PezosSandbox\Application\Members\Member;
use PezosSandbox\Application\Members\Members;
use PezosSandbox\Domain\Model\Member\AccessToken;
use PezosSandbox\Domain\Model\Member\Address;
use PezosSandbox\Domain\Model\Member\CouldNotFindMember;
use PezosSandbox\Infrastructure\Doctrine\Connection;
use PezosSandbox\Infrastructure\Doctrine\NoResult;
use PezosSandbox\Infrastructure\Mapping;

final class MembersUsingDoctrineDbal implements Members
{
    use Mapping;

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getOneByAccessToken(AccessToken $accessToken): Member
    {
        try {
            $data = $this->connection->selectOne(
                $this->connection
                    ->createQueryBuilder()
                    ->select('*')
                    ->from('members')
                    ->andWhere('accessToken = :accessToken')
                    ->setParameter('accessToken', $accessToken->asString()),
            );

            return $this->createMember($data);
        } catch (NoResult $exception) {
            throw CouldNotFindMember::withAccessToken($accessToken);
        }
    }

    public function getOneByAddress(Address $address): Member
    {
        try {
            $data = $this->connection->selectOne(
                $this->connection
                    ->createQueryBuilder()
                    ->select('*')
                    ->from('members')
                    ->andWhere('address = :address')
                    ->setParameter('address', $address->asString()),
            );

            return $this->createMember($data);
        } catch (NoResult $exception) {
            throw CouldNotFindMember::withAddress($address);
        }
    }

    /**
     * @param array<string,mixed> $data
     */
    private function createMember($data): Member
    {
        return new Member(self::asString($data, 'address'));
    }
}
