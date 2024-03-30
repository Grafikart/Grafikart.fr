<?php

namespace App\Domain\Coupon\Event;

use App\Domain\Coupon\Entity\Coupon;

readonly class CouponClaimedEvent
{

    public function __construct(private readonly Coupon $coupon){

    }

    public function getCoupon():Coupon
    {
        return $this->coupon;
    }

}
