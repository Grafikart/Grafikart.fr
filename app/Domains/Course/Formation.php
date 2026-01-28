<?php

namespace App\Domains\Course;

use App\Concerns\HasTechnologies;
use App\Domains\Attachment\Attachment;
use App\Domains\Course\Casts\AsDataCollection;
use App\Domains\Course\Factory\FormationFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property \Illuminate\Support\Collection<int, Chapter> $chapters
 * @property int[] $courseIds
 * @property int $duration
 * @property \Illuminate\Support\Collection<int, array{title: string, courses: Course[]}> $chaptersWithCourses
 */
class Formation extends Model
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

    protected function chaptersWithCourses(): Attribute
    {
        return Attribute::make(
            get: function () {
                $coursesByIds = $this->courses->keyBy('id');

                return $this->chapters->map(fn (Chapter $chapter) => [
                    'title' => $chapter->title,
                    'courses' => array_map(fn (int $id) => $coursesByIds[$id], $chapter->ids),
                ]);
            }
        );
    }
}
