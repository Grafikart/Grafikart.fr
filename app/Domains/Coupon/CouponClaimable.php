<?php

namespace App\Domains\Coupon;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CouponClaimable implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $code = trim((string) $value);
        if ($code === '') {
            return;
        }

        $coupon = Coupon::query()
            ->notClaimed()
            ->find($code);

        if ($coupon === null) {
            $fail('Ce coupon est invalide.');

            return;
        }

    }
}
