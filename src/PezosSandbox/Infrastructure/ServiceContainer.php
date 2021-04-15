<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure;

use Assert\Assert;
use PezosSandbox\Application\Application;
use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\EventDispatcher;
use PezosSandbox\Application\EventDispatcherWithSubscribers;

abstract class ServiceContainer
{
    protected ?EventDispatcher $eventDispatcher = null;

    protected ?ApplicationInterface $application = null;

    public function eventDispatcher(): EventDispatcher
    {
        if (null === $this->eventDispatcher) {
            $this->eventDispatcher = new EventDispatcherWithSubscribers();

            $this->registerEventSubscribers($this->eventDispatcher);
        }

        Assert::that($this->eventDispatcher)->isInstanceOf(
            EventDispatcher::class,
        );

        return $this->eventDispatcher;
    }

    public function application(): ApplicationInterface
    {
        if (null === $this->application) {
            $this->application = new Application();
        }

        return $this->application;
    }

    protected function registerEventSubscribers(
        EventDispatcherWithSubscribers $eventDispatcher
    ): void {
    }
}
