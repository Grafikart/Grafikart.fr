<?php

namespace App\Domains\Course;

use App\Concerns\HasRevisions;
use App\Concerns\HasTechnologies;
use App\Concerns\Media\HasMedia;
use App\Concerns\Media\RegisterMedia;
use App\Domains\Attachment\Attachment;
use App\Domains\Course\Factory\CourseFactory;
use App\Domains\Evaluation\Question;
use App\Domains\History\Progress;
use App\Domains\Revision\Revisionable;
use App\Helpers\MarkdownHelper;
use App\Infrastructure\Search\Contracts\Searchable;
use App\Infrastructure\Search\SearchDocument;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Course extends Model implements RegisterMedia, Revisionable, Searchable
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory;

    use HasMedia;
    use HasRevisions;
    use HasTechnologies;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'online',
        'attachment_id',
        'youtube_thumbnail_id',
        'deprecated_by_id',
        'formation_id',
        'duration',
        'video_size',
        'source_size',
        'youtube_id',
        'video_path',
        'source',
        'demo',
        'premium',
        'level',
        'force_redirect',
        'created_at',
    ];

    /**
     * @return BelongsTo<Attachment, $this>
     */
    public function attachment(): BelongsTo
    {
        return $this->belongsTo(Attachment::class);
    }

    /**
     * @return BelongsTo<Attachment, $this>
     */
    public function youtubeThumbnail(): BelongsTo
    {
        return $this->belongsTo(Attachment::class, 'youtube_thumbnail_id');
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function deprecatedBy(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'deprecated_by_id');
    }

    /**
     * @return BelongsTo<Formation, $this>
     */
    public function formation(): BelongsTo
    {
        return $this->belongsTo(Formation::class);
    }

    /**
     * @return HasMany<Question, $this>
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderByDesc('id');
    }

    /**
     * @return MorphMany<Progress, $this>
     */
    public function progress(): MorphMany
    {
        return $this->morphMany(Progress::class, 'progressable');
    }

    protected function casts(): array
    {
        return [
            'online' => 'boolean',
            'premium' => 'boolean',
            'force_redirect' => 'boolean',
            'level' => DifficultyLevel::class,
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    protected static function newFactory(): CourseFactory
    {
        return CourseFactory::new();
    }

    public static function registerMedia(): void
    {
        self::registerMediaForProperty(
            property: 'source',
            directory: fn (Course $model) => 'sources/'.ceil($model->id / 1000),
            filename: 'slug',
            disk: 'downloads',
            needId: true,
        );
    }

    public function toSearchDocument(): ?SearchDocument
    {
        if (! $this->online) {
            return null;
        }

        $title = $this->title;
        if ($this->formation !== null) {
            $title = $this->formation->title.': '.$title;
        }

        return new SearchDocument(
            id: (string) $this->id,
            title: $title,
            content: MarkdownHelper::text($this->content),
            category: $this->mainTechnologies->pluck('name')->all(),
            type: 'course',
            url: route('courses.show', ['slug' => $this->slug, 'course' => $this]),
            created_at: $this->created_at->getTimestamp(),
        );
    }

    public static function totalHours(): int
    {
        return ceil(self::query()->published(true)->selectRaw('SUM(duration) as duration')->value('duration') / 3600);
    }

    #[Scope]
    protected function published(Builder $query, $future = false): void
    {
        $query->where('online', true)->whereNull('deprecated_by_id');
        if (! $future) {
            $query->where('created_at', '<', now()->addDays(10));
        }
    }

    /**
     * Getters
     */
    public function videoUrl(): ?string
    {
        return $this->video_path ? ('/downloads/videos/'.$this->video_path) : null;
    }

    public function isPremium(): bool
    {
        return $this->premium;
    }

    public function isPublic(): bool
    {
        return ! $this->isScheduled() && ! $this->isPremium();
    }

    public function posterUrl(int $width, int $height): string
    {
        return $this->attachment?->url($width, $height) ?? '';
    }

    public function isScheduled(): bool
    {
        return $this->created_at->isFuture();
    }

    public function startTimeForUser(?User $user): int
    {
        if (! $user) {
            return 0;
        }
        $progress = Progress::where('user_id', $user->id)
            ->where('progressable_type', $this->getMorphClass())
            ->where('progressable_id', $this->id)
            ->first();
        if (! $progress || $progress->ratio >= 1) {
            return 0;
        }

        return (int) round($progress->ratio * $this->duration);
    }

    public function excerpt(): string
    {
        if ($this->content === null) {
            return '';
        }

        $parts = preg_split("/(\r\n|\r|\n){2}/", $this->content);

        return $parts === false ? '' : strip_tags($parts[0]);
    }

    public function filename(): string
    {
        return Str::slug(config('app.name').'_'.$this->title, '_');
    }
}
