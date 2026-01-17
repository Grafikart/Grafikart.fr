<?php

namespace App\Http\Cms\Data\Course;

use App\Domains\Course\DifficultyLevel;
use App\Http\Cms\Data\Attachment\AttachmentUrlData;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class CourseFormData extends Data
{
    public function __construct(
        public ?int $id = null,
        public string $title = '',
        public string $slug = '',
        public \DateTimeInterface $createdAt = new \DateTimeImmutable,
        public bool $online = false,
        public bool $premium = false,
        public bool $forceRedirect = false,
        public string $videoPath = '',
        public string $demo = '',
        public string $youtubeId = '',
        public int $duration = 0,
        public ?int $deprecatedBy = null,
        public string $content = '',
        #[WithCast(EnumCast::class)]
        public DifficultyLevel $level = DifficultyLevel::Junior,
        public bool $source = false,
        public ?AttachmentUrlData $attachment = null,
        public ?AttachmentUrlData $youtubeThumbnail = null,
        /** @var Collection<TechnologyUsageData> */
        public ?Collection $technologies = null,
    ) {
        $this->technologies ??= collect();
    }
}
