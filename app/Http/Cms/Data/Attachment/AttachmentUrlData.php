<?php

namespace App\Http\Cms\Data\Attachment;

use App\Concerns\Media\MapMedia;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class AttachmentUrlData extends Data
{
    public function __construct(
        public readonly int $id,
        #[MapMedia(property: 'name')]
        public readonly string $url,
    ) {}
}
