<?php

namespace App\Http\Admin\Data\Course;

use App\Component\ObjectMapper\Attribute\MapCollection;
use App\Domain\Course\DTO\ContentTechnologyDTO;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
readonly class CourseFormData
{

    public function __construct(
        public string $title = '',
        public string $slug = '',
        public \DateTimeInterface $createdAt = new \DateTimeImmutable(),
        public bool $online = false,
        public bool $premium = false,
        public bool $forceRedirect = false,
        public string $videoPath = '',
        public string $demo = '',
        public string $youtubeId = '',
        public int $duration = 0,
        public ?int $deprecatedBy = null,
        public string $content = '',
        public int $level = 1,
        public bool $source = false,
        #[MapCollection(item: ContentTechnologyDTO::class, source: 'technologyUsages')]
        /** @var ContentTechnologyDTO[] */
        public array $technologies = [],
        public ?CourseAttachmentData $image = null,
        public ?CourseAttachmentData $youtubeThumbnail = null,
    ) {
    }
}
