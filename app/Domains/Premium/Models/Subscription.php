<?php

namespace App\Domains\Premium\Models;

use App\Domains\Premium\Factory\SubscriptionFactory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property \DateTimeImmutable|null $next_payment
 */
class Subscription extends Model
{
    /** @use HasFactory<SubscriptionFactory> */
    use HasFactory;

    const int ACTIVE = 1;

    const int INACTIVE = 0;

    protected $fillable = [
        'user_id',
        'plan_id',
        'state',
        'next_payment',
        'stripe_id',
    ];

    protected function casts(): array
    {
        return [
            'state' => 'integer',
            'next_payment' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Plan, $this>
     */
    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function isActive(): bool
    {
        return $this->state === self::ACTIVE;
    }

    protected static function newFactory(): SubscriptionFactory
    {
        return SubscriptionFactory::new();
    }
}
