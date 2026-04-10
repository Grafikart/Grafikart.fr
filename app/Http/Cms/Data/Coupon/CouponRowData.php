<?php

namespace App\Http\Cms\Data\Coupon;

use App\Domains\Coupon\Coupon;
use DateTimeInterface;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class CouponRowData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly int $months,
        public readonly ?DateTimeInterface $claimedAt,
        public readonly DateTimeInterface $createdAt,
    ) {}

    public static function fromModel(Coupon $coupon): self
    {
        return new self(
            id: $coupon->id,
            email: $coupon->email,
            months: $coupon->months,
            claimedAt: $coupon->claimed_at,
            createdAt: $coupon->created_at,
        );
    }
}
