<?php

namespace App\Infrastructure\Search;

use App\Domains\Cms\Event\ContentCreatedEvent;
use App\Domains\Cms\Event\ContentDeletedEvent;
use App\Domains\Cms\Event\ContentUpdatedEvent;
use App\Infrastructure\Search\Contracts\IndexerInterface;
use App\Infrastructure\Search\Contracts\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Events\Dispatcher;

class IndexerSubscriber
{
    public function __construct(private readonly IndexerInterface $indexer) {}

    /**
     * @return array<class-string, string>
     */
    public function subscribe(Dispatcher $events): array
    {
        return [
            ContentCreatedEvent::class => 'handleCreated',
            ContentUpdatedEvent::class => 'handleUpdated',
            ContentDeletedEvent::class => 'handleDeleted',
        ];
    }

    public function handleCreated(ContentCreatedEvent $event): void
    {
        $this->index($event->item);
    }

    public function handleUpdated(ContentUpdatedEvent $event): void
    {
        $item = $event->item;

        if (! $item instanceof Searchable) {
            return;
        }

        $document = $item->toSearchDocument();

        if ($document !== null) {
            $this->indexer->index($document->toArray());
        } elseif ($item instanceof Model) {
            $this->indexer->remove((string) $item->getKey());
        }
    }

    public function handleDeleted(ContentDeletedEvent $event): void
    {
        $item = $event->item;

        if ($item instanceof Model) {
            $this->indexer->remove((string) $item->getKey());
        }
    }

    private function index(object $item): void
    {
        if (! $item instanceof Searchable) {
            return;
        }

        $document = $item->toSearchDocument();

        if ($document !== null) {
            $this->indexer->index($document->toArray());
        }
    }
}
