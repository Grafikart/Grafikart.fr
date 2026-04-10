<?php

namespace App\Http\Cms\Data\Coupon;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class CouponFormData extends Data
{
    public function __construct(
        public readonly string $id = '',
        public readonly int $months = 1,
    ) {}
}
