<?php

namespace App\Domains\Notification;

use App\Domains\Notification\Jobs\NotificationBroadcasterJob;
use App\Domains\Notification\Models\Notification;
use App\Helpers\UrlGenerator;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;

readonly class NotificationService
{
    public function __construct(
        public UrlGenerator $urlGenerator
    ) {}

    /**
     * Send a notification on a specific Channel
     */
    public function send(string $message, ?Model $model = null, ?User $user = null, string $channel = 'public', ?CarbonInterface $date = null, $url = null): Notification
    {
        $url ??= $model ? $this->urlGenerator->url($model) : '/';
        $notification = Notification::updateOrCreate([
            'url' => $url,
            'channel' => $channel,
            ...$model ? [
                'notifiable_id' => $model->getKey(),
                'notifiable_type' => $model->getMorphClass(),
            ] : [],
            ...$user ? [
                'user_id' => $user->id,
            ] : [],
        ], [
            'message' => $message,
            'created_at' => $date ?? now(),
        ]
        );
        NotificationBroadcasterJob::dispatch($notification)->delay($notification->created_at);

        return $notification;
    }

    public function broadcast() {}

    public function readAll(User $user): void
    {
        $user->notifications_read_at = now();
        $user->save();
        event(new NotificationReadEvent($user));
    }

    public function clean(): int
    {
        return Notification::query()
            ->where('created_at', '<', now()->subMonths(6))
            ->delete();
    }

    public function getChannelsForUser(User $user): array
    {
        $channels = [
            'user/'.$user->id,
            'public',
        ];

        if ($user->can('admin')) {
            $channels[] = 'admin';
        }

        return $channels;
    }
}
