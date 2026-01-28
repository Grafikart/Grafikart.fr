<?php

namespace App\Http\Front\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class FormationCourseData extends Data
{
    public readonly string $url;

    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $slug,
        public readonly int $duration,
    ) {
        $this->url = route('courses.show', ['slug' => $slug, 'course' => $this->id]);
    }
}
