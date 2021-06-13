<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class PubKeyConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PubKeyConstraint) {
            throw new UnexpectedTypeException($constraint, PubKeyConstraint::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!\is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        try {
        } catch (\InvalidArgumentException $exception) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(TokenMetadataConstraint::INVALID_FORMAT_ERROR)
                ->addViolation();
        }
    }
}
