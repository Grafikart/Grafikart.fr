<?php

namespace App\Http\Cms\Data;

use App\Domains\Notification\Jobs\NotificationBroadcasterJob;
use App\Infrastructure\Queue\FailedJob;
use App\Infrastructure\Queue\Job;
use Carbon\CarbonImmutable;
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
        if ($job->job instanceof NotificationBroadcasterJob) {
            $message = $job->job->notification->message;
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
