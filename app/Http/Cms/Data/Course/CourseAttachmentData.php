<?php

namespace App\Http\Cms\Data\Course;

use App\Component\ObjectMapper\Attribute\Map;
use App\Domain\Attachment\ObjectMapper\AttachmentUrlTransformer;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
final readonly class CourseAttachmentData
{
    public function __construct(
        public int $id,
        #[Map(source: 'id', transform: AttachmentUrlTransformer::class)]
        public string $url,
    ) {}
}
