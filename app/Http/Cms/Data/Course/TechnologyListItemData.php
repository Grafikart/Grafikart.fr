<?php

namespace App\Http\Cms\Data\Course;

use App\Component\ObjectMapper\Attribute\Map;
use App\Component\ObjectMapper\Transform\UrlTransformer;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class TechnologyListItemData
{
    public function __construct(
        public string $name,
        #[map(source: 'id', transform: urltransformer::class)]
        public string $url,
    ) {}

}
