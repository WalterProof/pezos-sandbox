<?php

declare(strict_types=1);

namespace Infrastructure;

use Assert\Assert;
use Application\Application;
use Application\ApplicationInterface;
use Application\EventDispatcher;
use Application\EventDispatcherWithSubscribers;

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

        Assert::that($this->eventDispatcher)->isInstanceOf(EventDispatcher::class);

        return $this->eventDispatcher;
    }

    public function application(): ApplicationInterface
    {
        if (null === $this->application) {
            $this->application = new Application();
        }

        return $this->application;
    }

    protected function registerEventSubscribers(EventDispatcherWithSubscribers $eventDispatcher): void
    {
    }
}
