<?php

namespace App\Http\API\Data\CourseFilter;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CourseFiltersResponse extends Data
{
    public function __construct(
        /** @var CourseFilterItem[] */
        public readonly array $technologies,
        /** @var CourseFilterItem[] */
        public readonly array $levels,
        public readonly CourseFilterTypeCount $types,
    ) {}
}
