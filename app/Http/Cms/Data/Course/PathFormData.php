<?php

namespace App\Http\Cms\Data\Course;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PathFormData extends Data
{
    public function __construct(
        public ?int $id = null,
        public string $title = '',
        public string $slug = '',
        public string $description = '',
        /** @var PathNodeData[] */
        public array $nodes = [],
    ) {}
}
