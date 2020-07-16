<?php

namespace App\Tests\Infrastructure\Mailing;

use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Event\MessageCreatedEvent;
use App\Domain\Forum\TopicService;
use App\Infrastructure\Mailing\ForumSubscriber;
use App\Infrastructure\Mailing\Mailer;
use App\Tests\EventSubscriberTest;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Mime\Email;

class ForumSubscriberTest extends EventSubscriberTest
{
    private function getSubscriber(): array
    {
        $mailer = $this->getMockBuilder(Mailer::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mailer->expects($this->any())->method('createEmail')->willReturn(new Email());
        $topicService = $this->getMockBuilder(TopicService::class)->disableOriginalConstructor()
            ->getMock();
        $subscriber = new ForumSubscriber(
            $mailer,
            $topicService
        );

        return [$subscriber, $mailer, $topicService];
    }

    public function testSubscribeToTheRightEvents(): void
    {
        $this->assertSubsribeTo(ForumSubscriber::class, MessageCreatedEvent::class);
    }

    public function testSendNoEmailIfNoOneToNotify(): void
    {
        /**
         * @var Subscriber $subscriber
         * @var MockObject $mailer
         * @var MockObject $topicService
         */
        [$subscriber, $mailer, $topicService] = $this->getSubscriber();
        $message = (new Message())
            ->setAuthor((new User())->setUsername('John Doe'))
            ->setTopic(new Topic());
        $topicService
            ->expects($this->once())
            ->method('usersToNotify')
            ->willReturn([]);
        $mailer->expects($this->never())->method('send');
        $this->dispatch($subscriber, new MessageCreatedEvent($message));
    }

    public function testSendTheRightAmountOfEmail(): void
    {
        /**
         * @var Subscriber $subscriber
         * @var MockObject $mailer
         * @var MockObject $topicService
         */
        [$subscriber, $mailer, $topicService] = $this->getSubscriber();
        $topicService
            ->expects($this->once())
            ->method('usersToNotify')
            ->willReturn([
                (new User())->setId(0)->setEmail('john1@doe.fr'),
                (new User())->setId(1)->setEmail('john2@doe.fr'),
            ]);
        $message = (new Message())
            ->setAuthor((new User())->setUsername('John Doe'))
            ->setTopic(new Topic());
        $mailer->expects($this->exactly(2))->method('send');
        $this->dispatch($subscriber, new MessageCreatedEvent($message));
    }
}
