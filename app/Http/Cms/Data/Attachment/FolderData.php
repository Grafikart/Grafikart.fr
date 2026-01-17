<?php

namespace App\Http\Cms\Data\Attachment;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class FolderData
{
    public function __construct(
        public string $path,
        public int $count,
    ) {}
}
