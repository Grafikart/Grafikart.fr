<?php

namespace App\Http\Admin\Data\Plan;

use App\Component\ObjectMapper\Attribute\Map;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

readonly class PlanFormInput
{
    public function __construct(
        #[Map]
        #[NotBlank]
        public string $name,
        #[Map]
        #[GreaterThanOrEqual(1)]
        public float $price,
        #[Map]
        #[GreaterThanOrEqual(1)]
        public int $duration,
        #[Map]
        public ?string $stripeId = null,
    ) {
    }
}
