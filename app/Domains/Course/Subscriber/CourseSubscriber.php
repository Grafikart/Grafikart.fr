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
            ContentCreatedEvent::class => 'handleContentCreated',
            ContentUpdatedEvent::class => 'handleContentUpdated',
        ];
    }

    public function handleContentCreated(ContentCreatedEvent $event): void
    {
        $this->dispatchJob($event->item);
    }

    public function handleContentUpdated(ContentUpdatedEvent $event): void
    {
        $this->dispatchJob($event->item);
    }

    private function dispatchJob(object $item): void
    {
        if ($item instanceof Course && $item->video_path && $item->wasChanged('video_path')) {
            ComputeCourseDurationJob::dispatch($item->id);
        }
    }
}
