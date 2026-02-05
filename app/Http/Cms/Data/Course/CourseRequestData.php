<?php

namespace App\Http\Cms\Data\Course;

use App\Concerns\AfterPersist;
use App\Domains\Cms\DataToModel;
use App\Domains\Course\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class CourseRequestData extends Data implements DataToModel
{
    use AfterPersist;

    public function __construct(
        #[Required]
        #[Min(2)]
        public readonly string $title,
        #[Required]
        #[Min(2)]
        public readonly string $slug,
        #[Required]
        public readonly string $content,
        #[Required]
        #[Min(0)]
        #[Max(2)]
        public readonly int $level,
        public readonly bool $online,
        public readonly bool $premium,
        public readonly bool $forceRedirect,
        #[Nullable]
        public readonly ?string $youtubeId,
        #[Nullable]
        public readonly ?string $videoPath,
        #[Nullable]
        public readonly ?string $demo,
        #[Nullable]
        #[Exists(table: 'courses', column: 'id')]
        public readonly ?int $deprecatedBy,
        #[Nullable]
        #[Exists(table: 'attachments', column: 'id')]
        public readonly ?int $image,
        #[Nullable]
        #[Exists(table: 'attachments', column: 'id')]
        public readonly ?int $youtubeThumbnail,
        public readonly ?\DateTimeImmutable $createdAt = null,
        /** @var array<TechnologyUsageData> */
        public readonly array $technologies = [],
        public readonly ?UploadedFile $source = null,
    ) {}

    public function toModel(Model $model): Model
    {
        assert($model instanceof Course);

        $model->fill([
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'level' => $this->level,
            'online' => $this->online,
            'premium' => $this->premium,
            'force_redirect' => $this->forceRedirect,
            'youtube_id' => $this->youtubeId,
            'video_path' => $this->videoPath,
            'demo' => $this->demo,
            'deprecated_by_id' => $this->deprecatedBy,
            'attachment_id' => $this->image,
            'youtube_thumbnail_id' => $this->youtubeThumbnail,
            'created_at' => $this->createdAt ?? now(),
        ]);

        $technologies = collect($this->technologies)->mapWithKeys(fn (TechnologyUsageData $technology) => [
            $technology->id => [
                'version' => $technology->version,
                'primary' => $technology->primary,
            ],
        ])->toArray();

        if ($this->source) {
            $model->source_size = $this->source->getSize();
            $model->attachMedia($this->source, 'source');
        }

        $this->afterPersist($model, function (Course $model) use ($technologies) {
            $model->technologies()->sync($technologies);
        });

        return $model;
    }
}
