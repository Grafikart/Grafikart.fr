<?php

namespace App\Http\Cms\Data\Course;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PathNodeEdgeData extends Data
{
    public function __construct(
        public int $id,
        public bool $primary = true,
    ) {}
}
