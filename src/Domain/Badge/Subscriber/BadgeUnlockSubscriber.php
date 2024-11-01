<?php

namespace App\Domain\Badge\Subscriber;

use App\Domain\Auth\User;
use App\Domain\Badge\BadgeService;
use App\Domain\Comment\CommentCreatedEvent;
use App\Domain\Comment\CommentRepository;
use App\Domain\Comment\Entity\Comment;
use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Event\TopicCreatedEvent;
use App\Domain\Forum\Event\TopicResolvedEvent;
use App\Domain\Premium\Event\PremiumSubscriptionEvent;
use App\Domain\Revision\Event\RevisionAcceptedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class BadgeUnlockSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly BadgeService $service, private readonly EntityManagerInterface $em)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CommentCreatedEvent::class => 'onCommentCreated',
            TopicCreatedEvent::class => 'onTopicCreated',
            TopicResolvedEvent::class => 'onTopicSolved',
            LoginSuccessEvent::class => 'onLogin',
            PremiumSubscriptionEvent::class => 'onPremium',
            RevisionAcceptedEvent::class => 'onRevision',
        ];
    }

    public function onCommentCreated(CommentCreatedEvent $event): void
    {
        $author = $event->getComment()->getAuthor();
        if (!$author) {
            return;
        }
        /** @var CommentRepository $repository */
        $repository = $this->em->getRepository(Comment::class);
        $this->service->unlock($author, 'comments', $repository->count(['author' => $author]));
    }

    public function onTopicCreated(TopicCreatedEvent $event): void
    {
        $author = $event->getTopic()->getAuthor();
        /** @var CommentRepository $repository */
        $repository = $this->em->getRepository(Topic::class);
        $this->service->unlock($author, 'topics', $repository->count(['author' => $author]));
    }

    public function onTopicSolved(TopicResolvedEvent $event): void
    {
        $author = $event->getMessage()->getAuthor();
        /** @var CommentRepository $repository */
        $repository = $this->em->getRepository(Message::class);
        $this->service->unlock($author, 'answers', $repository->count([
            'author' => $author,
            'accepted' => true,
        ]));
    }

    public function onLogin(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!($user instanceof User)) {
            return;
        }
        $this->service->unlock($user, 'years', (int) $user->getCreatedAt()->diff(new \DateTimeImmutable())->format('%y'));
    }

    public function onPremium(PremiumSubscriptionEvent $event): void
    {
        $this->service->unlock($event->getUser(), 'premium');
    }

    public function onRevision(RevisionAcceptedEvent $event): void
    {
        $this->service->unlock($event->getRevision()->getAuthor(), 'revision');
    }
}
