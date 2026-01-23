<?php

namespace App\Http\Cms\Data\Course;

use App\Domains\Course\DifficultyLevel;
use App\Http\Cms\Data\Attachment\AttachmentUrlData;
use App\Http\Cms\Data\MapChapters;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\EnumCast;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class FormationFormData extends Data
{
    public function __construct(
        public ?int $id = null,
        public string $title = '',
        public string $slug = '',
        public \DateTimeInterface $createdAt = new \DateTimeImmutable,
        public bool $online = false,
        public bool $forceRedirect = false,
        public ?int $deprecatedBy = null,
        public string $content = '',
        public ?string $short = null,
        #[WithCast(EnumCast::class)]
        public DifficultyLevel $level = DifficultyLevel::Junior,
        public ?AttachmentUrlData $attachment = null,
        public ?string $youtubePlaylist = null,
        public ?string $links = null,
        /** @var ChapterData[] */
        #[MapChapters]
        public array $chapters = [],
        /** @var Collection<TechnologyUsageData> */
        public ?Collection $technologies = null,
    ) {
        $this->chapters ??= collect();
        $this->technologies ??= collect();
    }

}
