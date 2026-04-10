<?php

namespace App\Domains\Badge;

use App\Domains\Badge\Factory\BadgeFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Badge extends Model
{
    /** @use HasFactory<BadgeFactory> */
    use HasFactory;

    protected $table = 'badges';

    protected $fillable = [
        'name',
        'description',
        'position',
        'action',
        'action_count',
        'theme',
        'image',
        'unlockable',
    ];

    protected function casts(): array
    {
        return [
            'unlockable' => 'boolean',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('created_at');
    }

    protected static function newFactory(): BadgeFactory
    {
        return BadgeFactory::new();
    }
}
