<?php

namespace App\Http\Cms\Data\Attachment;

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
