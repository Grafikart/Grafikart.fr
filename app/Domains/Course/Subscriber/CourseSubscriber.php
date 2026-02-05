<?php

namespace App\Domains\Course\Subscriber;

use App\Domains\Cms\Event\ContentCreatedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Domains\Course\Course;
use App\Domains\Course\Job\ComputeCourseDurationJob;
use Illuminate\Events\Dispatcher;

class CourseSubscriber
{
    public function subscribe(Dispatcher $events): array
    {
        return [
            ContentCreatedEvent::class => 'dispatchJob',
            ContentUpdatedEvent::class => 'dispatchJob',
        ];
    }

    public function dispatchJob(ContentCreatedEvent|ContentUpdatedEvent $event): void
    {
        $item = $event->item;
        if (! $item instanceof Course) {
            return;
        }

        if ($item->video_path && $item->wasChanged('video_path')) {
            ComputeCourseDurationJob::dispatch($item->id);
        }
    }
}
