<?php

namespace App\Http\Cms\Data\Attachment;

use App\Concerns\Media\MapMedia;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final class AttachmentFileData extends Data
{
    public function __construct(
        public readonly int $id,
        public readonly \DateTimeInterface $createdAt,
        public readonly string $name,
        #[MapMedia(property: 'name')]
        public readonly string $url,
        public readonly int $size,
        #[MapMedia(property: 'name')]
        public readonly string $thumbnail,
    ) {}
}
