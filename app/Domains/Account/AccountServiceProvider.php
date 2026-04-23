<?php

namespace App\Domains\Account;

use App\Domains\Account\Listeners\UpdateLastLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AccountServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(Login::class, UpdateLastLogin::class);
    }
}
