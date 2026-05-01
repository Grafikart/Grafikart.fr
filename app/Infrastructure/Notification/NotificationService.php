<?php

namespace App\Infrastructure\Notification;

use App\Domains\Notification\Jobs\NotificationBroadcasterJob;
use App\Helpers\UrlGenerator;
use App\Infrastructure\Notification\Events\NotificationReadEvent;
use App\Models\User;

readonly class NotificationService
{
    public function __construct(
        public UrlGenerator $urlGenerator
    ) {}

    public function readAll(User $user): void
    {
        $user->notifications_read_at = now();
        $user->save();
        event(new NotificationReadEvent($user));
    }

    public function clean(): int
    {
        return SiteNotification::query()
            ->where('created_at', '<', now()->subMonths(6))
            ->delete();
    }
}
