<?php

namespace App\Domains\Course;

use App\Concerns\HasTechnologies;
use App\Domains\Attachment\Attachment;
use App\Domains\Course\Casts\AsDataCollection;
use App\Domains\Course\Factory\FormationFactory;
use App\Domains\History\Progress;
use App\Helpers\MarkdownHelper;
use App\Infrastructure\Search\Contracts\Searchable;
use App\Infrastructure\Search\SearchDocument;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;

/**
 * @property \Illuminate\Support\Collection<int, Chapter> $chapters
 * @property Collection<int, int> $courseIds
 * @property int $duration
 * @property \Illuminate\Support\Collection<int, array{title: string, courses: Course[]}> $chaptersWithCourses
 */
class Formation extends Model implements Searchable
{
    /** @use HasFactory<FormationFactory> */
    use HasFactory;

    use HasTechnologies;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'online',
        'attachment_id',
        'short',
        'chapters',
        'youtube_playlist',
        'links',
        'level',
        'deprecated_by_id',
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
     * @return BelongsTo<Formation, $this>
     */
    public function deprecatedBy(): BelongsTo
    {
        return $this->belongsTo(Formation::class, 'deprecated_by_id');
    }

    protected function casts(): array
    {
        return [
            'online' => 'boolean',
            'force_redirect' => 'boolean',
            'chapters' => AsDataCollection::of(Chapter::class),
            'level' => DifficultyLevel::class,
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    protected static function newFactory(): FormationFactory
    {
        return FormationFactory::new();
    }

    /**
     * @return HasMany<Course, $this>
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    /**
     * @return MorphMany<Progress, $this>
     */
    public function progress(): MorphMany
    {
        return $this->morphMany(Progress::class, 'progressable');
    }

    protected function courseIds(): Attribute
    {
        return Attribute::make(
            get: fn () => collect($this->chapters)->pluck('ids')->flatten()
        );
    }

    protected function duration(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->courses->sum('duration')
        );
    }

    /**
     * @return Attribute<Collection<array{title: string, courses: list<Course>}>, never>
     */
    protected function chaptersWithCourses(): Attribute
    {
        return Attribute::make(get: function (): Collection {
            $coursesByIds = $this->courses->keyBy('id');

            return $this->chapters->map(fn (Chapter $chapter) => [
                'title' => $chapter->title,
                'courses' => array_map(fn (int $id): Course => $coursesByIds[$id], $chapter->ids),
            ]);
        });
    }

    public function toSearchDocument(): ?SearchDocument
    {
        if (! $this->online) {
            return null;
        }

        return new SearchDocument(
            id: (string) $this->id,
            title: $this->title,
            content: MarkdownHelper::text($this->content),
            category: $this->mainTechnologies->pluck('name')->all(),
            type: 'formation',
            url: route('formations.show', $this),
            created_at: $this->created_at->getTimestamp(),
        );
    }

    #[Scope]
    protected function published(Builder $query, $future = false): void
    {
        $query->where('online', true)->whereNull('deprecated_by_id');
        if (! $future) {
            $query->where('created_at', '<', now());
        }
    }

    public function hasYoutubeLink(): bool
    {
        return str_contains($this->content ?? '', 'youtube.com/');
    }

    public function icon(): ?string
    {
        return $this->technology()?->mediaUrl('image');
    }

    public function nextCourse(int $courseId): ?Course
    {
        $currentIndex = $this->courseIds->search($courseId);

        if ($currentIndex === false) {
            return null;
        }
        $nextCourseId = $this->courseIds[$currentIndex + 1] ?? null;

        if (! $nextCourseId) {
            return null;
        }

        return $this->relationLoaded('courses') ?
            $this->courses->firstWhere('id', $nextCourseId) :
            $this->courses()->find($nextCourseId);
    }
}
