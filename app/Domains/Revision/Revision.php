<?php

namespace App\Domains\Revision;

use App\Domains\Revision\Factory\RevisionFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Revision extends Model
{
    /** @use HasFactory<RevisionFactory> */
    use HasFactory;

    protected $fillable = [
        'content',
        'state',
        'comment',
    ];

    /** @return BelongsTo<User, $this> */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return MorphTo<Model, $this> */
    public function revisionable(): MorphTo
    {
        return $this->morphTo();
    }

    protected function casts(): array
    {
        return [
            'state' => RevisionStatus::class,
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    protected static function newFactory(): RevisionFactory
    {
        return RevisionFactory::new();
    }

    #[Scope]
    protected function pending(Builder $query): Builder
    {
        return $query->where('state', RevisionStatus::Pending);
    }

    public static function existsForUser(User $user): bool
    {
        return self::where('user_id', $user->id)->exists();
    }
}
