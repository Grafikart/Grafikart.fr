<?php

namespace App\Domains\Coupon;

use App\Models\User;

class CouponService
{
    public function claim(string $code, User $user): Coupon
    {
        $coupon = Coupon::query()
            ->notClaimed()
            ->findOrFail($code);

        $user->extendsPremium($coupon->months);
        $user->save();

        $coupon->user()->associate($user);
        $coupon->claimed_at = now();
        $coupon->save();

        return $coupon;
    }
}
