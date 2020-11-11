<?php

namespace App\Tests\Infrastructure\Search\EventSubscriber;

use App\Domain\Application\Entity\Content;
use App\Domain\Application\Event\ContentCreatedEvent;
use App\Domain\Application\Event\ContentDeletedEvent;
use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Infrastructure\Search\EventSubscriber\IndexerSubscriber;
use App\Infrastructure\Search\IndexerInterface;
use App\Tests\EventSubscriberTest;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class IndexerSubscriberTest extends EventSubscriberTest
{
    public function testSubscribeToRightEvents()
    {
        $this->assertSubscribeTo(IndexerSubscriber::class, ContentUpdatedEvent::class);
        $this->assertSubscribeTo(IndexerSubscriber::class, ContentCreatedEvent::class);
        $this->assertSubscribeTo(IndexerSubscriber::class, ContentDeletedEvent::class);
    }

    public function testDoesNotIndexIfNoChanges()
    {
        $normalizer = $this->createMock(NormalizerInterface::class);
        $indexer = $this->createMock(IndexerInterface::class);
        $indexer->expects($this->never())->method('index');
        $normalizer->expects($this->any())->method('normalize')->willReturnOnConsecutiveCalls(
            ['a' => 'demo'],
            ['a' => 'demo'],
        );
        $subscriber = new IndexerSubscriber($indexer, $normalizer);
        $event = new ContentUpdatedEvent($this->createMock(Content::class), $this->createMock(Content::class));
        $this->dispatch($subscriber, $event);
    }

    public function testIndexCorrectlyIfNormalizedDataChanges()
    {
        $normalizer = $this->createMock(NormalizerInterface::class);
        $indexer = $this->createMock(IndexerInterface::class);
        $indexer->expects($this->once())->method('index')->with(['a' => 'demo2']);
        $normalizer->expects($this->any())->method('normalize')->willReturnOnConsecutiveCalls(
            ['a' => 'demo'],
            ['a' => 'demo2'],
        );
        $subscriber = new IndexerSubscriber($indexer, $normalizer);
        $event = new ContentUpdatedEvent($this->createMock(Content::class), $this->createMock(Content::class));
        $this->dispatch($subscriber, $event);
    }

    public function testUnindexWhenContentDeleted()
    {
        $normalizer = $this->createMock(NormalizerInterface::class);
        $indexer = $this->createMock(IndexerInterface::class);
        $indexer->expects($this->once())->method('remove')->with('100');
        $subscriber = new IndexerSubscriber($indexer, $normalizer);
        $content = $this->createMock(Content::class);
        $content->expects($this->any())->method('getId')->willReturn(100);
        $event = new ContentDeletedEvent($content);
        $this->dispatch($subscriber, $event);
    }

    public function testIndexWhenContentCreated()
    {
        $normalizer = $this->createMock(NormalizerInterface::class);
        $indexer = $this->createMock(IndexerInterface::class);
        $indexer->expects($this->once())->method('index')->with(['a' => 'demo2']);
        $normalizer->expects($this->any())->method('normalize')->willReturn(['a' => 'demo2']);
        $subscriber = new IndexerSubscriber($indexer, $normalizer);
        $event = new ContentCreatedEvent($this->createMock(Content::class));
        $this->dispatch($subscriber, $event);
    }
}
