<?php

namespace App\Http\Admin\Data\Plan;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class PlanItemData
{
    public function __construct(
        public int $id,
        public string $name,
        public float $price,
        public int $duration,
        public ?string $stripeId,
    ) {
    }
}
