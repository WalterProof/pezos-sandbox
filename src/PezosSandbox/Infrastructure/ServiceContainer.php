<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure;

use Assert\Assert;
use PezosSandbox\Application\AccessPolicy;
use PezosSandbox\Application\Application;
use PezosSandbox\Application\ApplicationInterface;
use PezosSandbox\Application\Clock;
use PezosSandbox\Application\EventDispatcher;
use PezosSandbox\Application\EventDispatcherWithSubscribers;
use PezosSandbox\Application\Exchanges\Exchanges;
use PezosSandbox\Application\Members\Members;
use PezosSandbox\Application\Tokens\Tokens;
use PezosSandbox\Domain\Model\Exchange\ExchangeRepository;
use PezosSandbox\Domain\Model\Member\MemberRepository;
use PezosSandbox\Domain\Model\Tag\TagRepository;
use PezosSandbox\Domain\Model\Token\TokenRepository;
use Test\Acceptance\FakeClock;

abstract class ServiceContainer
{
    protected ?ApplicationInterface $application = null;
    protected ?EventDispatcher $eventDispatcher  = null;

    private ?Clock $clock = null;

    public function eventDispatcher(): EventDispatcher
    {
        if (null === $this->eventDispatcher) {
            $this->eventDispatcher = new EventDispatcherWithSubscribers();

            $this->registerEventSubscribers($this->eventDispatcher);
        }

        Assert::that($this->eventDispatcher)->isInstanceOf(
            EventDispatcher::class
        );

        return $this->eventDispatcher;
    }

    public function application(): ApplicationInterface
    {
        if (null === $this->application) {
            $this->application = new Application(
                $this->exchangeRepository(),
                $this->memberRepository(),
                $this->tokenRepository(),
                $this->eventDispatcher(),
                $this->exchanges(),
                $this->members(),
                $this->tokens(),
                $this->clock()
            );
        }

        return $this->application;
    }

    protected function clock(): Clock
    {
        if (null === $this->clock) {
            $this->clock = new FakeClock();
        }

        return $this->clock;
    }

    protected function registerEventSubscribers(
        EventDispatcherWithSubscribers $eventDispatcher
    ): void {
        /* $eventDispatcher->subscribeToSpecificEvent(AccessWasGranted::class, [ */
        /*     $this->accessPolicy(), */
        /*     'whenAccessWasGranted', */
        /* ]); */
    }

    abstract protected function exchangeRepository(): ExchangeRepository;

    abstract protected function memberRepository(): MemberRepository;

    abstract protected function tagRepository(): TagRepository;

    abstract protected function tokenRepository(): TokenRepository;

    abstract protected function exchanges(): Exchanges;

    abstract protected function members(): Members;

    abstract protected function tokens(): Tokens;
}
