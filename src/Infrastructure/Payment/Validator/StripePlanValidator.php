<?php

namespace App\Infrastructure\Payment\Validator;

use App\Infrastructure\Payment\Stripe\StripeApi;
use Stripe\Exception\InvalidRequestException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class StripePlanValidator extends ConstraintValidator
{
    public function __construct(private readonly StripeApi $api)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof StripePlan) {
            throw new \RuntimeException('Contrainte inattendue');
        }
        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', '')
                ->addViolation();
        }

        try {
            $this->api->getPlan($value);
        } catch (InvalidRequestException) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
