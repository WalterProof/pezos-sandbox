<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Validation;

use Symfony\Component\Validator\Constraint;

class TokenMetadataConstraint extends Constraint
{
    const INVALID_FORMAT_ERROR = '815a3602-d1f5-11eb-b8bc-0242ac130003';

    public $message = 'token_metadata.invalid';

    protected static $errorNames = [
        self::INVALID_FORMAT_ERROR => 'INVALID_FORMAT_ERROR',
    ];
}
