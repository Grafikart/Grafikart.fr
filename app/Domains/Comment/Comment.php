<?php

namespace App\Domains\Comment;

use App\Domains\Comment\Factory\CommentFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    protected $fillable = [
        'email',
        'username',
        'content',
    ];

    public function getUpdatedAtColumn()
    {
        return null;
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function commentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function username(): Attribute
    {
        return Attribute::get(function () {
            return $this->user?->name ?? $this->attributes['username'] ?? null;
        });
    }

    /**
     * @return Attribute<string|null, never>
     */
    protected function email(): Attribute
    {
        return Attribute::get(fn () => $this->user?->email ?? $this->attributes['email'] ?? null);
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'immutable_datetime',
        ];
    }

    protected static function newFactory(): CommentFactory
    {
        return CommentFactory::new();
    }

    #[Scope]
    protected function Suspicious(Builder $query): Builder
    {
        return $query
            ->whereLike('content', '%http%');
    }
}
