<?php

namespace App\Domains\Course;

use App\Domains\Course\Factory\PathFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Path extends Model
{
    /** @use HasFactory<PathFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'tags',
        'online',
        'created_at',
    ];

    /**
     * @return HasMany<PathNode, $this>
     */
    public function nodes(): HasMany
    {
        return $this->hasMany(PathNode::class);
    }

    protected function casts(): array
    {
        return [
            'online' => 'boolean',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    protected static function newFactory(): PathFactory
    {
        return PathFactory::new();
    }

    #[Scope]
    protected function published(Builder $query, $future = false): void
    {
        $query->where('online', true);
        if (! $future) {
            $query->where('created_at', '<', now());
        }
    }
}
