<?php

namespace App\Domains\Notification\Subscriber;

use App\Domains\Cms\Event\ContentCreatedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Domains\Course\Course;
use App\Domains\Course\Formation;
use App\Domains\Notification\NotificationService;
use Illuminate\Events\Dispatcher;

class ContentSubscriber
{
    public function __construct(private NotificationService $service) {}

    public function subscribe(Dispatcher $events): array
    {
        return [
            ContentCreatedEvent::class => 'onCreate',
            ContentUpdatedEvent::class => 'onCreate',
        ];
    }

    public function onCreate(ContentCreatedEvent|ContentUpdatedEvent $event): void
    {
        $content = $event->content;
        if (
            ($content instanceof Course || $content instanceof Formation)
            && $content->wasChanged('online') && $content->online
            && $content->created_at->isFuture()
        ) {
            $this->notifyContent($content);
        }
    }

    private function notifyContent(Course|Formation $content): void
    {
        $technologies = $content->mainTechnologies->pluck('name')->implode(', ');
        $duration = duration($content->duration);

        if ($content instanceof Course) {
            $message = "Nouveau tutoriel {$technologies} !<br> <strong>{$content->title}</strong> <strong>({$duration})</strong>";
        } else {
            $message = "Nouvelle formation {$technologies} disponible :  <strong>{$content->title}</strong>";
        }

        $this->service->send(
            message: $message,
            model: $content,
            date: $content->created_at
        );
    }
}
