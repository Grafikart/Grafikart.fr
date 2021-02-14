<?php

namespace App\Domain\Comment;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\AuthService;
use App\Http\Api\Resource\CommentResource;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class CommentService
{
    private AuthService $auth;

    private EntityManagerInterface $em;

    private EventDispatcherInterface $dispatcher;

    public function __construct(AuthService $auth, EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->auth = $auth;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public function create(CommentResource $data): Comment
    {
        // On crée un nouveau commentaire
        /** @var Content $target */
        $target = $this->em->getRepository(Content::class)->find($data->getTarget());
        /** @var Comment|null $parent */
        $parent = $data->getParent() ? $this->em->getReference(Comment::class, $data->getParent()) : null;
        $comment = (new Comment())
            ->setAuthor($this->auth->getUserOrNull())
            ->setUsername($data->getUsername())
            ->setEmail($data->getEmail())
            ->setCreatedAt(new \DateTime())
            ->setContent($data->getContent())
            ->setParent($parent)
            ->setTarget($target)
        ;
        $this->em->persist($comment);
        $this->em->flush();
        $this->dispatcher->dispatch(new CommentCreatedEvent($comment));

        return $comment;
    }

    public function update(Comment $comment, string $content): Comment
    {
        $comment->setContent($content);
        $this->em->flush();

        return $comment;
    }

    public function delete(int $commentId): void
    {
        /** @var Comment $comment */
        $comment = $this->em->getReference(Comment::class, $commentId);
        $this->em->remove($comment);
        $this->em->flush();
    }
}
