<?php

declare(strict_types=1);

namespace PezosSandbox\Application;

interface EventDispatcher
{
    public function dispatch(object $event): void;

    /**
     * @param array<object> $events
     */
    public function dispatchAll(array $events): void;
}
