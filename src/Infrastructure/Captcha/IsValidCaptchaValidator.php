<?php

namespace App\Infrastructure\Captcha;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class IsValidCaptchaValidator extends ConstraintValidator
{
    public function __construct(private readonly CaptchaKeyService $service)
    {
    }

    private function setAsInvalid(IsValidCaptcha $constraint): void
    {
        $this->context->buildViolation($constraint->message)
            ->addViolation();
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsValidCaptcha) {
            throw new UnexpectedTypeException($constraint, IsValidCaptcha::class);
        }

        if (empty($value) || !is_string($value) || !preg_match('/^\d{1,3}-\d{1,3}$/', $value)) {
            $this->setAsInvalid($constraint);

            return;
        }

        try {
            $isValid = $this->service->verifyKey($value);
            if (!$isValid) {
                $this->setAsInvalid($constraint);

                return;
            }
        } catch (TooManyTryException) {
            $this->setAsInvalid($constraint);

            return;
        }
    }
}
