<?php

namespace App\Http\Cms;

use App\Infrastructure\Twitch\TwitchAPI;
use Illuminate\Http\RedirectResponse;

final readonly class TwitchController
{
    public function store(TwitchAPI $api): RedirectResponse
    {
        $api->addWebhookSubscription();

        return back()->with('success', 'Les webhooks Twitch ont bien été enregistrés');
    }
}
