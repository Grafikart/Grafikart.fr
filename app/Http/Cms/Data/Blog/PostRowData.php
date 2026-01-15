<?php

namespace App\Http\Cms\Data\Blog;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class PostRowData
{
    public function __construct(
        public int $id,
        public string $title,
        public bool $online,
    ) {
    }
}
