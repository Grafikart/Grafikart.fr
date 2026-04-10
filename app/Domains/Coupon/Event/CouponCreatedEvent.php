<?php

namespace App\Domains\Coupon\Event;

use App\Domains\Coupon\Coupon;

class CouponCreatedEvent
{
    public function __construct(public readonly Coupon $coupon) {}

}
