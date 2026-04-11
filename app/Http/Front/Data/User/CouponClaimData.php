<?php

namespace App\Http\Front\Data\User;

use App\Domains\Coupon\CouponClaimable;
use Spatie\LaravelData\Data;

class CouponClaimData extends Data
{
    public function __construct(
        public string $coupon,
    ) {}

    public static function rules(): array
    {
        return [
            'coupon' => ['required', 'string', new CouponClaimable],
        ];
    }
}
