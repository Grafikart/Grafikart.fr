<?php

namespace App\Http\Cms\Data\Course;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PathNodeMetaData extends Data
{
    public function __construct(
        public ?string $video = null,
    ) {}
}
