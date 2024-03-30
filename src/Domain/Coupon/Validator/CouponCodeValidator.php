<?php

namespace App\Domain\Coupon\Validator;

use App\Domain\Coupon\Repository\CouponRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CouponCodeValidator extends ConstraintValidator
{
    public function __construct(private readonly CouponRepository $couponRepository)
    {
    }

    /**
     * @param ?string $value
     */
    public function validate($value, Constraint $constraint)
    {
        if (!($constraint instanceof CouponCode)) {
            throw new \RuntimeException(sprintf("%s ne peut valider que des %s", self::class, CouponCode::class));
        }
        if (null === $value || '' === $value) {
            return;
        }

        $coupon = $this->couponRepository->find($value);

        if (!$coupon || $coupon->isClaimed()) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
