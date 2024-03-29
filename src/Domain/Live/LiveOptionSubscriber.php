<?php

declare(strict_types=1);

namespace App\Domain\Live;

use App\Domain\Application\Event\OptionUpdatedEvent;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LiveOptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly CacheItemPoolInterface $cache)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OptionUpdatedEvent::class => 'onOptionUpdated',
        ];
    }

    public function onOptionUpdated(OptionUpdatedEvent $event): void
    {
        if (LiveService::OPTION_KEY === $event->getOption()->getKey()) {
            $this->cache->deleteItem(LiveService::OPTION_KEY);
        }
    }
}
