<?php

namespace App\Http\Admin\Data\Course;

use App\Component\ObjectMapper\Attribute\MapCollection;
use App\Domain\Course\DTO\ContentTechnologyDTO;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class CourseFormData
{

    public function __construct(
        public int $id,
        public string $title,
        public string $slug,
        public \DateTimeInterface $createdAt,
        public bool $online,
        public bool $premium,
        public bool $forceRedirect,
        public string $videoPath,
        public string $demo,
        public string $youtubeId,
        public int $duration,
        public ?int $deprecatedBy,
        public string $content,
        public int $level,
        public bool $source,
        #[MapCollection(item: ContentTechnologyDTO::class, source: 'technologyUsages')]
        /** @var ContentTechnologyDTO[] */
        public array $technologies,
        public ?CourseAttachmentData $image,
        public ?CourseAttachmentData $youtubeThumbnail,
    ) {
    }
}
