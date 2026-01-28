<?php

namespace App\Http\Front\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class FormationChapterData extends Data
{
    public function __construct(
        public readonly string $title,
        /** @var FormationCourseData[] */
        public readonly array $courses = [],
    ) {}
}
