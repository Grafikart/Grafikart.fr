<?php

namespace App\Http\Cms\Data\Blog;

use App\Http\Cms\Data\Attachment\AttachmentUrlData;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PostFormData extends Data
{
    public function __construct(
        public ?int $id = null,
        public string $title = '',
        public string $slug = '',
        public \DateTimeInterface $createdAt = new \DateTimeImmutable,
        public ?int $categoryId = null,
        public bool $online = false,
        public ?AttachmentUrlData $attachment = null,
        public string $content = '',
    ) {}
}
