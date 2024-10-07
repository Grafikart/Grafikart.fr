<?php

namespace App\Domain\Coupon\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Vérifie la validité d'un coupon (il doit être non réclamé).
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD)]
class CouponCode extends Constraint
{
    public string $message = "Ce coupon n'existe pas ou n'est plus valide";
}
