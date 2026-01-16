<?php

namespace App\Domains\Course\Models;

use App\Concerns\Media\HasMedia;
use App\Concerns\Media\WithMedia;
use App\Domains\Course\Factory\TechnologyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Technology extends Model implements HasMedia
{
    /** @use HasFactory<TechnologyFactory> */
    use HasFactory;

    use WithMedia;

    protected $fillable = [
        'name',
        'slug',
        'content',
        'image',
        'type',
    ];

    /**
     * Technologies that are required for this technology.
     *
     * @return BelongsToMany<Technology, $this>
     */
    public function requirements(): BelongsToMany
    {
        return $this->belongsToMany(
            Technology::class,
            'technology_requirement',
            'technology_id',
            'requirement_id'
        );
    }

    /**
     * Technologies that require this technology.
     *
     * @return BelongsToMany<Technology, $this>
     */
    public function dependents(): BelongsToMany
    {
        return $this->belongsToMany(
            Technology::class,
            'technology_requirement',
            'requirement_id',
            'technology_id'
        );
    }

    protected static function newFactory(): TechnologyFactory
    {
        return TechnologyFactory::new();
    }

    public function registerMedia(): void
    {
        $this->registerMediaForProperty(
            property: 'image',
            directory: 'icons',
            filename: 'slug',
        );
    }
}
