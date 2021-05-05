<?php

namespace PezosSandbox\Infrastructure\Symfony\Validation;

use Bzzhh\Pezos\Validator;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class PubKeyConstraintValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof PubKeyConstraint) {
            throw new UnexpectedTypeException(
                $constraint,
                LeanpubInvoiceIdConstraint::class,
            );
        }

        if ($value === null || $value === '') {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        $validator = new Validator();
        if (!$validator->validatePubKey($value)) {
            $this->context
                ->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(PubKeyConstraint::INVALID_FORMAT_ERROR)
                ->addViolation();
        }
    }
}
