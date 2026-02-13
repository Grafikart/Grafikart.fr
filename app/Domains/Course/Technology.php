<?php

namespace App\Domains\Course;

use App\Concerns\Media\HasMedia;
use App\Concerns\Media\RegisterMedia;
use App\Domains\Course\Factory\TechnologyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Technology extends Model implements RegisterMedia
{
    /** @use HasFactory<TechnologyFactory> */
    use HasFactory;

    use HasMedia;

    protected $fillable = [
        'name',
        'slug',
        'content',
        'image',
        'type',
        'deprecated',
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

    /**
     * @return BelongsToMany<Formation, $this>
     */
    public function formations(): BelongsToMany
    {
        return $this->belongsToMany(Formation::class)
            ->withPivot(['version', 'primary'])
            ->wherePivot('primary', true);
    }

    /**
     * @return BelongsToMany<Course, $this>
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)
            ->withPivot(['version', 'primary'])
            ->wherePivot('primary', true);
    }

    protected static function newFactory(): TechnologyFactory
    {
        return TechnologyFactory::new();
    }

    public static function registerMedia(): void
    {
        self::registerMediaForProperty(
            property: 'image',
            directory: 'icons',
            filename: 'slug',
        );
    }
}
