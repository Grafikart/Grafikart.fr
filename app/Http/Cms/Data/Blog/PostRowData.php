<?php

namespace App\Http\Cms\Data\Blog;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PostRowData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly bool $online,
    ) {}
}
