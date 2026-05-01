<?php

namespace App\Domains\Coupon;

use App\Domains\Coupon\Factory\CouponFactory;
use App\Domains\School\School;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property CarbonImmutable|null $claimed_at
 */
class Coupon extends Model
{
    /** @use HasFactory<CouponFactory> */
    use HasFactory;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'school_id',
        'user_id',
        'claimed_at',
        'email',
        'months',
    ];

    protected function casts(): array
    {
        return [
            'months' => 'integer',
            'claimed_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    #[Scope]
    protected function claimed(Builder $query): void
    {
        $query->whereNotNull('claimed_at');
    }

    #[Scope]
    protected function notClaimed(Builder $query): void
    {
        $query->whereNull('claimed_at');
    }

    protected static function newFactory(): CouponFactory
    {
        return CouponFactory::new();
    }
}
