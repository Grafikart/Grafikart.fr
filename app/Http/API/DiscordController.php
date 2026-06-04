<?php

namespace App\Http\API;

use App\Models\User;
use Illuminate\Support\Collection;

class DiscordController
{
    /**
     * List the discord ids of all premium users (allow the bot to assign the role to users)
     */
    public function premium(): Collection
    {
        return User::query()
            ->wherePremium()
            ->whereNotNull('discord_id')
            ->whereNot('discord_id', '')
            ->pluck('discord_id');
    }
}
