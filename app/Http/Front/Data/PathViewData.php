<?php

namespace App\Http\Front\Data;

use App\Http\Cms\Data\Course\PathNodeData;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PathViewData extends Data
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly string $title = '',
        public string $slug = '',
        public string $tags = '',
        public readonly string $description = '',
    ) {}

    public function tags(): array
    {
        return array_map(fn(string $tag) => trim($tag), explode(',', $this->tags));
    }
}
