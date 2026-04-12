<?php

namespace App\Http\Cms\Data;

use App\Infrastructure\Notification\Channel\HasSiteNotification;
use App\Infrastructure\Queue\FailedJob;
use App\Infrastructure\Queue\Job;
use Carbon\CarbonImmutable;
use Illuminate\Notifications\SendQueuedNotifications;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class JobItemData extends Data
{
    public function __construct(
        public int $id,
        public string $type,
        public string $message,
        public ?string $exception,
        public CarbonImmutable $date,
    ) {}

    public static function fromModel(Job $job): self
    {
        $message = $job->name;

        // For site notification, extract the message from the notification
        if ($job->job instanceof SendQueuedNotifications && $job->job->notification instanceof HasSiteNotification) {
            $notification = $job->job->notification;
            assert($notification instanceof HasSiteNotification);
            $notifiable = $job->job->notifiables->firstOrFail();
            $message = $notification->toSiteNotification($notifiable)->message;
        }

        $date = $job instanceof FailedJob ? $job->failed_at : $job->available_at;
        assert($date instanceof CarbonImmutable);

        return new self(
            id: $job->id,
            type: $job->name,
            message: $message,
            exception: $job->exception ?? null,
            date: $date,
        );
    }
}
