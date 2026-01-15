<?php

namespace App\Http\Admin\Data\Plan;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class PlanFormData
{
    public function __construct(
        public string $name = '',
        public float $price = 0,
        public int $duration = 0,
        public ?string $stripeId = null,
    ) {
    }
}
