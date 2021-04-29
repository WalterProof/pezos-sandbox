<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure;

use Doctrine\DBAL\Connection as DbalConnection;
use PezosSandbox\Application\Members\Members;
use PezosSandbox\Domain\Model\Member\MemberRepository;
use PezosSandbox\Infrastructure\Doctrine\Connection;
use PezosSandbox\Infrastructure\TalisOrm\EventDispatcherAdapter;
use PezosSandbox\Infrastructure\TalisOrm\MembersUsingDoctrineDbal;
use PezosSandbox\Infrastructure\TalisOrm\MemberTalisOrmRepository;
use TalisOrm\AggregateRepository;

class ProductionServiceContainer extends ServiceContainer
{
    private DbalConnection $dbalConnection;

    public function __construct(DbalConnection $connection)
    {
        $this->dbalConnection = $connection;
    }

    protected function memberRepository(): MemberRepository
    {
        return new MemberTalisOrmRepository(
            $this->talisOrmAggregateRepository(),
        );
    }

    protected function members(): Members
    {
        return new MembersUsingDoctrineDbal($this->connection());
    }

    private function talisOrmAggregateRepository(): AggregateRepository
    {
        return new AggregateRepository(
            $this->dbalConnection,
            new EventDispatcherAdapter($this->eventDispatcher()),
        );
    }

    private function connection(): Connection
    {
        return new Connection($this->dbalConnection);
    }
}
