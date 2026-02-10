<?php

namespace App\Http\API\Data\CourseFilter;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CourseFilterItem extends Data
{
    public function __construct(
        public readonly string $label,
        public readonly string $value,
        public readonly ?int $courses_count = null,
        public readonly ?int $formations_count = null,
    ) {}
}
