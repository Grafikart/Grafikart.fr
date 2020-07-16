<?php

namespace App\Domain\Attachment\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class AttachmentExistValidator extends ConstraintValidator
{
    /**
     * @param mixed           $value
     * @param AttachmentExist $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$value instanceof NonExistingAttachment) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->setParameter('{{ id }}', (string) $value->getId())
            ->addViolation();
    }
}
