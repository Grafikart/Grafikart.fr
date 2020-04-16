<?php

namespace App\Infrastructure\Search\EventSubscriber;

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
            ContentUpdatedEvent::class => 'indexContent'
        ];
    }

    public function indexContent(ContentUpdatedEvent $event): void
    {
        /** @var array{id: string, title: string, content: string, created_at: int, category: string[]} $data */
        $data = $this->normalizer->normalize($event->getContent(), 'search');
        $this->indexer->index($data);
    }
}
