<?php

namespace App\Http\Cms\Data\Course;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PathRowData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
    ) {}
}
