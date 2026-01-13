<?php

namespace App\Http\Data;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class AttachmentUrlData
{
    public function __construct(
        public int $id,
        public string $url,
    ) {
    }
}
