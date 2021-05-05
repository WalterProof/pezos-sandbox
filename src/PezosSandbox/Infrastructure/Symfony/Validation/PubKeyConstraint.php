<?php

namespace PezosSandbox\Infrastructure\Symfony\Validation;

use Symfony\Component\Validator\Constraint;

class PubKeyConstraint extends Constraint
{
    const INVALID_FORMAT_ERROR = 'b78de379-b68d-4fc2-b9f1-90367e7d81b3';

    public $message = 'pub_key.invalid';

    protected static $errorNames = [
        self::INVALID_FORMAT_ERROR => 'INVALID_FORMAT_ERROR',
    ];
}
