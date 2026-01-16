<?php

namespace App\Http\Cms\Data\Course;

use App\Component\ObjectMapper\Attribute\Map;
use App\Component\ObjectMapper\Attribute\MapEntity;
use App\Domain\Attachment\Attachment;
use App\Domain\Course\DTO\ContentTechnologyDTO;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Transform\TechnologyUsageTransform;
use App\Validator\Exists;
use App\Validator\Slug;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

readonly class CourseFormInput
{
    public function __construct(
        #[Map]
        public \DateTimeInterface $createdAt,
        #[NotBlank()]
        #[Map]
        public string $title,
        #[Map]
        #[NotBlank()]
        public string $content,
        #[Map]
        #[NotBlank()]
        #[Range(min: 0, max: 2)]
        public int $level,
        #[Map]
        public bool $online,
        #[Map]
        public ?string $youtubeId,
        #[Map]
        public bool $premium,
        #[Map]
        public bool $forceRedirect,
        #[Map]
        public ?string $videoPath,
        #[Map]
        public ?string $demo,
        #[MapEntity(Course::class)]
        #[Exists(class: Course::class)]
        public ?int $deprecatedBy,
        #[Map]
        #[NotBlank]
        #[Slug]
        public string $slug,
        /** @var ContentTechnologyDTO[] */
        #[Map(transform: TechnologyUsageTransform::class)]
        public ?array $technologies,
        #[MapEntity(Attachment::class)]
        #[Exists(class: Attachment::class)]
        public ?int $image,
        #[MapEntity(Attachment::class)]
        #[Exists(class: Attachment::class)]
        public ?int $youtubeThumbnail,
    ) {}
}
