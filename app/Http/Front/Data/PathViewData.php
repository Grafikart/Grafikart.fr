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
        public readonly string $description = '',
        /** @var PathNodeData[] */
        public readonly array $nodes = [],
    ) {}

}
