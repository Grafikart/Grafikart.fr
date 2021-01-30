<?php

namespace App\Tests\Infrastructure\Spam\Subscriber;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Event\PreTopicCreatedEvent;
use App\Helper\OptionManagerInterface;
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
        $optionManager = $this->createMock(OptionManagerInterface::class);
        $optionManager->expects($this->any())->method('get')->with('spam_words')->willReturn('casino
homework');
        $subscriber = new SpamSubscriber($optionManager);
        $this->dispatch($subscriber, new PreTopicCreatedEvent($topic));
        $this->assertSame(true, $topic->isSpam());
    }
}
