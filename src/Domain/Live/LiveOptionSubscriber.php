<?php declare(strict_types=1);

namespace App\Domain\Live;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Domain\Application\Event\OptionUpdatedEvent;

class LiveOptionSubscriber implements EventSubscriberInterface
{

    private CacheItemPoolInterface $cache;

    public function __construct(CacheItemPoolInterface $cache)
    {

        $this->cache = $cache;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            OptionUpdatedEvent::class => 'onOptionUpdated',
        ];
    }

    public function onOptionUpdated(OptionUpdatedEvent $event): void
    {
        if ($event->getOption()->getKey() === LiveService::OPTION_KEY) {
            $this->cache->deleteItem(LiveService::OPTION_KEY);
        }
    }
}
