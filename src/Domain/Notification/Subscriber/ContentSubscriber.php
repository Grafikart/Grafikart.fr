<?php

namespace App\Domain\Notification\Subscriber;

use App\Core\Helper\TimeHelper;
use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use App\Domain\Notification\NotificationService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentSubscriber implements EventSubscriberInterface
{
    private NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentUpdatedEvent::class => 'onUpdate',
        ];
    }

    public function onUpdate(ContentUpdatedEvent $event): void
    {
        $content = $event->getContent();
        if ($content instanceof Course &&
            true === $content->isOnline() &&
            false === $event->getPrevious()->isOnline() &&
            $content->getCreatedAt() < new \DateTimeImmutable()
        ) {
            $technologies = implode(', ', array_map(fn (Technology $t) => $t->getName(), $content->getMainTechnologies()));
            $duration = TimeHelper::duration($content->getDuration());
            $this->service->notifyChannel('public', "Nouveau tutoriel {$technologies} !<br> <strong>{$content->getTitle()}</strong> <em>({$duration})</em>", $content);
        }
    }
}
