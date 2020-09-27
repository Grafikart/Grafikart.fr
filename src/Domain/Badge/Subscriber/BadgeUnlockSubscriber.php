<?php

namespace App\Domain\Badge\Subscriber;

use App\Domain\Badge\BadgeService;
use App\Domain\Comment\Comment;
use App\Domain\Comment\CommentCreatedEvent;
use App\Domain\Comment\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BadgeUnlockSubscriber implements EventSubscriberInterface
{

    private BadgeService $service;
    private EntityManagerInterface $em;

    public function __construct(BadgeService $service, EntityManagerInterface $em)
    {
        $this->service = $service;
        $this->em = $em;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CommentCreatedEvent::class => 'onCommentCreated'
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
}
