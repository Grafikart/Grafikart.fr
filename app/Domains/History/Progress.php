<?php

namespace App\Domains\History;

use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\History\Factory\ProgressFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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
        return $this->morphTo();
    }

    protected function casts(): array
    {
        return [
            'progress' => 'integer',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    protected static function newFactory(): ProgressFactory
    {
        return ProgressFactory::new();
    }
}
