<?php

namespace App\Tests\Domain\Live;

use App\Domain\Application\Entity\Option;
use App\Domain\Application\Event\OptionUpdatedEvent;
use App\Domain\Live\LiveOptionSubscriber;
use App\Domain\Live\LiveService;
use App\Tests\EventSubscriberTest;
use Psr\Cache\CacheItemPoolInterface;

class LiveOptionSubscriberTest extends EventSubscriberTest
{
    public function testSubscribeToEvents()
    {
        $this->assertSubscribeTo(LiveOptionSubscriber::class, OptionUpdatedEvent::class);
    }

    public function testEventClearTheCache()
    {
        $option = (new Option())->setKey(LiveService::OPTION_KEY)->setValue('aze');
        $event = new OptionUpdatedEvent($option);
        $cache = $this->createMock(CacheItemPoolInterface::class);
        $subscriber = new LiveOptionSubscriber($cache);
        $cache->expects($this->once())->method('deleteItem');
        $this->dispatch($subscriber, $event);
    }

    public function testEventDoesNotClearTheCache()
    {
        $option = (new Option())->setKey('azeaze')->setValue('aze');
        $event = new OptionUpdatedEvent($option);
        $cache = $this->createMock(CacheItemPoolInterface::class);
        $subscriber = new LiveOptionSubscriber($cache);
        $cache->expects($this->never())->method('deleteItem');
        $this->dispatch($subscriber, $event);
    }
}
