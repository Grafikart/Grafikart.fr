<?php

namespace App\Infrastructure\Notification;

use App\Models\User;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SiteNotification extends Model
{
    public const null UPDATED_AT = null;
    protected $table = 'notifications';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'created_at' => 'immutable_datetime',
        ];
    }

    #[Scope]
    protected function forUser(Builder $query, User $user): Builder
    {
        return $query
            ->where('created_at', '<=', now())
            ->where('channel', 'public')
            ->where(function (Builder $builder) use ($user) {
                $builder->whereNull('user_id')->orWhere('user_id', $user->id);
            });
    }
}
