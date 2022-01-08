<?php

namespace App\Domain\Notification\Subscriber;

use App\Domain\Application\Entity\Content;
use App\Domain\Application\Event\ContentCreatedEvent;
use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Formation;
use App\Domain\Course\Entity\Technology;
use App\Domain\Notification\NotificationService;
use App\Helper\TimeHelper;
use App\Infrastructure\Queue\EnqueueMethod;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ContentSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly NotificationService $service, private readonly EnqueueMethod $enqueueMethod)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentUpdatedEvent::class => 'onUpdate',
            ContentCreatedEvent::class => 'onCreate',
        ];
    }

    /**
     * Quand un tutoriel/formation passe en ligne, on envoie une notification globale.
     */
    public function onUpdate(ContentUpdatedEvent $event): void
    {
        $content = $event->getContent();
        if (($content instanceof Course || $content instanceof Formation) &&
            true === $content->isOnline() &&
            false === $event->getPrevious()->isOnline()
        ) {
            $this->notifyContent($content);
        }
    }

    /**
     * Quand un tutoriel/formation est créé en ligne, on envoie une notification globale.
     */
    public function onCreate(ContentCreatedEvent $event): void
    {
        $content = $event->getContent();
        if (($content instanceof Course || $content instanceof Formation)
            && true === $content->isOnline()
        ) {
            $this->notifyContent($content);
        }
    }

    private function notifyContent(\App\Domain\Course\Entity\Course|\App\Domain\Course\Entity\Formation $content): void
    {
        $technologies = implode(', ', array_map(fn (Technology $t) => $t->getName(), $content->getMainTechnologies()));
        $duration = TimeHelper::duration($content->getDuration());

        if ($content instanceof Course) {
            $message = "Nouveau tutoriel {$technologies} !<br> <strong>{$content->getTitle()}</strong> <strong>({$duration})</strong>";
        } else {
            $message = "Nouvelle formation {$technologies} disponible :  <strong>{$content->getTitle()}</strong>";
        }

        // Le contenu est publié de suite
        if ($content->getCreatedAt() < new \DateTimeImmutable()) {
            $this->service->notifyChannel('public', $message, $content);
        } else {
            $this->enqueueMethod->enqueue(NotificationService::class, 'notifyChannel', [
                'public',
                $message,
                $content,
            ], $content->getCreatedAt());
        }
    }
}
