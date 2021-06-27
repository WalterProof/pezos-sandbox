<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\Symfony\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class TokenMetadataConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof TokenMetadataConstraint) {
            throw new UnexpectedTypeException($constraint, TokenMetadataConstraint::class);
        }

        if (!\is_array($value)) {
            throw new UnexpectedValueException($value, 'array');
        }

        try {
            // TODO: refacto this into model with a Metadata object Metadata::fromArray(...)
            if (
                array_diff_key(
                    array_flip(['decimals', 'symbol', 'name']),
                    $value
                )
            ) {
                throw new \InvalidArgumentException('some keys are missing');
            }
            if (
                null === $value['decimals'] ||
                '' === $value['decimals'] ||
                empty($value['symbol'] || empty($value['name']))
            ) {
                throw new \InvalidArgumentException('some values are missing');
            }
        } catch (\InvalidArgumentException $exception) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ message }}', $exception->getMessage())
                ->setCode(TokenMetadataConstraint::INVALID_FORMAT_ERROR)
                ->addViolation();
        }
    }
}
