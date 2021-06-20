<?php

declare(strict_types=1);

namespace PezosSandbox\Domain\Model\Exchange;

use PezosSandbox\Domain\Model\Common\AbstractUserFacingError;

final class CouldNotFindExchange extends AbstractUserFacingError
{
    public static function withId(ExchangeId $exchangeId): self
    {
        return new self('id.does_not_exist', [
            '{exchangeId}' => $exchangeId->asString(),
        ]);
    }

    public static function withName(string $name): self
    {
        return new self('name.does_not_exist', [
            '{name}' => $name,
        ]);
    }
}
