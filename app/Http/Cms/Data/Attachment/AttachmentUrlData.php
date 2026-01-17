<?php

namespace App\Http\Cms\Data\Attachment;

use App\Concerns\Media\MapMedia;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class AttachmentUrlData
{
    public function __construct(
        public int $id,
        #[MapMedia(property: 'name')]
        public string $url,
    ) {}
}
