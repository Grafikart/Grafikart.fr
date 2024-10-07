<?php

namespace App\Domain\Coupon\DTO;

use App\Domain\Auth\User;
use App\Domain\Coupon\Validator\CouponCode;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Demande d'activation d'un coupon.
 */
class CouponClaimDTO
{
    public function __construct(
        private readonly User $user,
        #[Assert\NotBlank()]
        #[CouponCode()]
        private string $code = '',
    ) {
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): CouponClaimDTO
    {
        $this->code = $code;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
