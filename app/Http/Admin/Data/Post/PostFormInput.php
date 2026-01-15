<?php

namespace App\Http\Admin\Data\Post;

use App\Component\ObjectMapper\Attribute\Map;
use App\Component\ObjectMapper\Attribute\MapEntity;
use App\Domain\Attachment\Attachment;
use App\Domain\Blog\Category;
use App\Validator\Exists;
use App\Validator\Slug;
use Symfony\Component\Validator\Constraints\NotBlank;

readonly class PostFormInput
{
    public function __construct(
        #[NotBlank]
        #[Map]
        public string $title,
        #[NotBlank]
        #[Slug]
        #[Map]
        public string $slug,
        #[Map]
        public \DateTimeInterface $createdAt,
        #[MapEntity(Category::class)]
        #[Exists(class: Category::class)]
        public ?int $category,
        #[Map]
        public bool $online,
        #[MapEntity(Attachment::class)]
        #[Exists(class: Attachment::class)]
        public ?int $image,
        #[NotBlank]
        #[Map]
        public string $content,
    ) {
    }
}
