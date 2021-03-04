<?php

namespace App\Infrastructure\Search\EventSubscriber;

use App\Domain\Application\Event\ContentCreatedEvent;
use App\Domain\Application\Event\ContentDeletedEvent;
use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Infrastructure\Search\IndexerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class IndexerSubscriber implements EventSubscriberInterface
{
    private IndexerInterface $indexer;
    private NormalizerInterface $normalizer;

    public function __construct(IndexerInterface $indexer, NormalizerInterface $normalizer)
    {
        $this->indexer = $indexer;
        $this->normalizer = $normalizer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ContentUpdatedEvent::class => 'updateContent',
            ContentCreatedEvent::class => 'indexContent',
            ContentDeletedEvent::class => 'removeContent',
        ];
    }

    public function indexContent(ContentCreatedEvent $event): void
    {
        /** @var array{id: string, title: string, content: string, created_at: int, category: string[]} $content */
        $content = $this->normalizer->normalize($event->getContent(), 'search');
        if ($event->getContent()->isOnline()) {
            $this->indexer->index($content);
        }
    }

    public function removeContent(ContentDeletedEvent $event): void
    {
        $this->indexer->remove((string) $event->getContent()->getId());
    }

    public function updateContent(ContentUpdatedEvent $event): void
    {
        $previous = $event->getPrevious();
        $current = $event->getContent();
        /** @var array{id: string, title: string, content: string, created_at: int, category: string[]} $previousData */
        $previousData = $this->normalizer->normalize($previous, 'search');
        /** @var array{id: string, title: string, content: string, created_at: int, category: string[]} $data */
        $data = $this->normalizer->normalize($current, 'search');
        if ($current->isOnline() && ($previousData !== $data || false === $previous->isOnline())) {
            $this->indexer->index($data);
        } elseif (true === $previous->isOnline() && false === $current->isOnline()) {
            $this->indexer->remove((string) $current->getId());
        }
    }
}
