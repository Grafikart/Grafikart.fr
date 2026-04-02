<?php

namespace App\Domains\Course;

use App\Domains\Course\Factory\PathNodeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property object{primary: bool} $pivot
 */
class PathNode extends Model
{
    /** @use HasFactory<PathNodeFactory> */
    use HasFactory;

    protected $fillable = [
        'path_id',
        'icon',
        'title',
        'description',
        'content_type',
        'content_id',
        'meta',
        'x',
        'y',
    ];

    /**
     * @return BelongsTo<Path, $this>
     */
    public function path(): BelongsTo
    {
        return $this->belongsTo(Path::class);
    }

    /**
     * @return MorphTo<Model, $this>
     */
    public function content(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return BelongsToMany<PathNode, $this>
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(PathNode::class, 'path_node_links', 'child_id', 'parent_id')
            ->withPivot('primary');
    }

    /**
     * @return BelongsToMany<PathNode, $this>
     */
    public function children(): BelongsToMany
    {
        return $this->belongsToMany(PathNode::class, 'path_node_links', 'parent_id', 'child_id')
            ->withPivot('primary');
    }

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'x' => 'float',
            'y' => 'float',
        ];
    }

    protected static function newFactory(): PathNodeFactory
    {
        return PathNodeFactory::new();
    }
}
