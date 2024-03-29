<?php

namespace App\Tests\Infrastructure\Spam\Subscriber;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Event\PreTopicCreatedEvent;
use App\Infrastructure\Spam\SpamService;
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
        $this->assertSubscribeTo(SpamSubscriber::class, $eventName);
    }

    public function testFlagAsSpamCorrectly(): void
    {
        $user = new User();
        $topic = (new Topic())
            ->setAuthor($user)
            ->setContent(<<<MARKDOWN
                Bonjour,

                Casino en ligne !

                Voila je rencontre un petit problème avec mon code.
            MARKDOWN);
        $spamService = $this->createMock(SpamService::class);
        $spamService->expects($this->any())->method('words')->willReturn(['casino', 'homework']);
        $subscriber = new SpamSubscriber($spamService);
        $this->dispatch($subscriber, new PreTopicCreatedEvent($topic));
        $this->assertSame(true, $topic->isSpam());
    }
}
