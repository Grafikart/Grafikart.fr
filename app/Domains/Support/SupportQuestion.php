<?php

namespace App\Domains\Support;

use App\Domains\Course\Course;
use App\Domains\Support\Factory\SupportQuestionFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportQuestion extends Model
{
    /** @use HasFactory<SupportQuestionFactory> */
    use HasFactory;

    protected $table = 'support_questions';

    protected $fillable = [
        'user_id',
        'title',
        'content',
        'answer',
        'online',
        'course_id',
        'timestamp',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    protected function casts(): array
    {
        return [
            'online' => 'boolean',
            'timestamp' => 'integer',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    protected static function newFactory(): SupportQuestionFactory
    {
        return SupportQuestionFactory::new();
    }

    public function hasAnswer(): bool
    {
        return filled(trim((string) $this->answer));
    }
}
