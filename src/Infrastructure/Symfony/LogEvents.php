<?php
declare(strict_types=1);

namespace Infrastructure\Symfony;

use Infrastructure\Event;
use Psr\Log\LoggerInterface;

final class LogEvents
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function notify(object $event): void
    {
        $this->logger->debug(
            'An event was dispatched: ' . Event::asString($event)
        );
    }
}
