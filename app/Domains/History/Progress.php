<?php

namespace App\Domains\History;

use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\History\Factory\ProgressFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $ratio
 */
class Progress extends Model
{
    /** @use HasFactory<ProgressFactory> */
    use HasFactory;

    protected $table = 'progress';

    protected $fillable = [
        'user_id',
        'progressable_id',
        'progressable_type',
        'progress',
        'score',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return MorphTo<Course|Formation, $this>
     */
    public function progressable(): MorphTo
    {
        /** @phpstan-ignore return.type */
        return $this->morphTo();
    }

    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'score' => 'integer',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    protected static function newFactory(): ProgressFactory
    {
        return ProgressFactory::new();
    }

    protected function ratio(): Attribute
    {
        return Attribute::make(
            get: fn () => round($this->progress / 1000)
        );
    }

    #[Scope]
    protected function completed(Builder $query): void
    {
        $query->where('progress', 1000);
    }
}
