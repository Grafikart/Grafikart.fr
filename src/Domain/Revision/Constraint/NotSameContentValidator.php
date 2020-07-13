<?php

namespace App\Domain\Revision\Constraint;

use App\Domain\Revision\Revision;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NotSameContentValidator extends ConstraintValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof NotSameContent) {
            throw new UnexpectedTypeException($constraint, NotSameContent::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof Revision) {
            throw new UnexpectedValueException($value, Revision::class);
        }

        if (null !== $value->getTarget() && $value->getContent() === $value->getTarget()->getContent()) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
