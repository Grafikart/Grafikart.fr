<?php

namespace App\Http\API\Data;

use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class APISearchResponse extends Data
{
    public function __construct(
        /** @var APISearchItem[] */
        public readonly array $items,
        public readonly int $hits,
    ) {}
}
