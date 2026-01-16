<?php

namespace App\Http\Cms\Data\Blog;

use App\Http\Cms\Data\Attachment\AttachmentUrlData;
use App\Http\Cms\Data\OptionItemData;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class PostFormData
{
    public function __construct(
        public ?int $id = null,
        public string $title = '',
        public string $slug = '',
        public \DateTimeInterface $createdAt = new \DateTimeImmutable(),
        public ?int $category = null,
        public bool $online = false,
        public ?AttachmentUrlData $image = null,
        public string $content = '',
        /** @var OptionItemData[] */
        public array $categories = [],
    ) {
    }
}
