<?php

namespace App\Http\Cms\Data\Course;

use App\Concerns\AfterPersist;
use App\Domains\Cms\DataToModel;
use App\Domains\Course\Chapter;
use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class FormationRequestData extends Data implements DataToModel
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
        public readonly bool $forceRedirect,
        #[Nullable]
        public readonly ?string $short,
        #[Nullable]
        public readonly ?string $youtubePlaylist,
        #[Nullable]
        public readonly ?string $links,
        #[Nullable]
        #[Exists(table: 'formations', column: 'id')]
        public readonly ?int $deprecatedBy,
        #[Nullable]
        #[Exists(table: 'attachments', column: 'id')]
        public readonly ?int $image,
        public readonly ?\DateTimeImmutable $createdAt = null,
        /** @var array<Chapter> */
        public readonly array $chapters = [],
        /** @var array<TechnologyUsageData> */
        public readonly array $technologies = [],
    ) {}

    public function toModel(Model $model): Model
    {
        assert($model instanceof Formation);

        $model->fill([
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'level' => $this->level,
            'online' => $this->online,
            'force_redirect' => $this->forceRedirect,
            'short' => $this->short,
            'youtube_playlist' => $this->youtubePlaylist,
            'links' => $this->links,
            'deprecated_by_id' => $this->deprecatedBy,
            'attachment_id' => $this->image,
            'chapters' => $this->chapters,
            'created_at' => $this->createdAt ?? now(),
        ]);

        $technologies = collect($this->technologies)->mapWithKeys(fn (TechnologyUsageData $technology) => [
            $technology->id => [
                'version' => $technology->version,
                'primary' => $technology->primary,
            ],
        ])->toArray();

        $this->afterPersist($model, function (Formation $model) use ($technologies) {
            $model->technologies()->sync($technologies);
            Course::whereIn('id', $model->courseIds)->update(['formation_id' => $model->id]);
        });

        return $model;
    }
}
