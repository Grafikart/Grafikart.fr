<?php

namespace App\Models;

use App\Domains\History\Progress;
use App\Domains\Premium\Models\Transaction;
use App\Models\Factory\UserFactory;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property CarbonImmutable|null $premium_end_at
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\App\Models\Factory\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'country',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'immutable_datetime',
            'notifications_read_at' => 'immutable_datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'premium_end_at' => 'immutable_datetime',
            'last_login_at' => 'immutable_datetime',
        ];
    }

    public function isPremium(): bool
    {
        return $this->premium_end_at !== null && $this->premium_end_at->isFuture();
    }

    public function extendsPremium(int $month): void
    {
        $premiumEnd = $this->premium_end_at?->isFuture() ? $this->premium_end_at : now();
        $this->premium_end_at = $premiumEnd->addMonths($month);
    }

    /**
     * @return HasMany<Progress, $this>
     */
    public function progress(): HasMany
    {
        return $this->hasMany(Progress::class);
    }

    /**
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    public function isAdmin(): bool
    {
        if (config('app.env') !== 'production') {
            return $this->name === 'Grafikart';
        }

        return $this->name === 'Grafikart' && $this->id = 1;
    }

    public static function findAdmin(): self
    {
        return self::query()->where('name', 'Grafikart')->firstOrFail();
    }
}
