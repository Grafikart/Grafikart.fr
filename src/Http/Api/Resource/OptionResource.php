<?php

namespace App\Http\Api\Resource;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class OptionResource
{
    public function __construct(
        public int $id,
        public string $name,
    ) {
    }
}
