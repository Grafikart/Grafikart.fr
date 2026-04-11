<?php

namespace App\Http\Front;

use App\Domains\Coupon\CouponService;
use App\Http\Front\Data\User\CouponClaimData;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CouponController
{
    public function claim(CouponClaimData $data, Request $request, CouponService $couponService): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);

        $coupon = $couponService->claim($data->coupon, $user);

        return to_route('users.edit')->with('success', sprintf('Vous avez obtenu %d mois de premium', $coupon->months));
    }
}
