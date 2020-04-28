<?php

namespace App\Tests\Infrastructure\Spam\Subscriber;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Event\PreTopicCreatedEvent;
use App\Domain\Forum\Repository\TopicRepository;
use App\Infrastructure\Spam\Subscriber\SpamSubscriber;
use App\Tests\EventSubscriberTest;

class SpamSubscriberTest extends EventSubscriberTest
{

    public function getEvents(): iterable
    {
        yield [PreTopicCreatedEvent::class];
    }

    /**
     * @dataProvider getEvents
     */
    public function testSubscribeToCorrectEvents(string $eventName): void
    {
        $this->assertSubsribeTo(SpamSubscriber::class, $eventName);
    }

    public function getData () : iterable {
        yield [2, true];
        yield [3, true];
        yield [4, false];
    }

    /**
     * @dataProvider getData
     */
    public function testFlagAsSpamCorrectly (int $topicCount, bool $expectedSpam): void {
        $user = new User();
        $repository = $this->getMockBuilder(TopicRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
        $repository->expects($this->once())->method('countForUser')->with($user)->willReturn($topicCount);
        $topic = (new Topic())
            ->setAuthor($user);
        ;
        $subscriber = new SpamSubscriber($repository);
        $this->dispatch($subscriber, new PreTopicCreatedEvent($topic));
        $this->assertSame($expectedSpam, $topic->isSpam());

    }

}
