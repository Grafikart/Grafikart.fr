<?php

namespace App\Http\Cms\Data\Blog;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PostRowData extends Data
{
    public function __construct(
        readonly public int $id,
        readonly public string $title,
        readonly public bool $online,
    ) {
    }
}
