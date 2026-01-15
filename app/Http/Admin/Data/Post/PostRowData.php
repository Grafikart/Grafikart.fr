<?php

namespace App\Http\Admin\Data\Post;

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
