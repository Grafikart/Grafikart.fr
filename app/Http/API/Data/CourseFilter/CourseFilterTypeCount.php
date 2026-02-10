<?php

namespace App\Http\API\Data\CourseFilter;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CourseFilterTypeCount extends Data
{
    public function __construct(
        public readonly int $course,
        public readonly int $formation,
    ) {}
}
