<?php

namespace App\Http\Api\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Domain\Application\Entity\Content;
use App\Domain\Auth\AuthService;
use App\Domain\Auth\User;
use App\Domain\Comment\Comment;
use App\Domain\Comment\CommentCreatedEvent;
use App\Http\Api\Resource\CommentResource;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Security;

class CommentPersister implements ContextAwareDataPersisterInterface
{
    private DataPersisterInterface $decoratedDataPersister;
    private ValidatorInterface $validator;
    private Security $security;
    private EntityManagerInterface $entityManager;
    private AuthService $auth;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        DataPersisterInterface $decoratedDataPersister,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        Security $security,
        AuthService $auth,
        EventDispatcherInterface $dispatcher
    ) {
        $this->validator = $validator;
        $this->security = $security;
        $this->decoratedDataPersister = $decoratedDataPersister;
        $this->entityManager = $entityManager;
        $this->auth = $auth;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param object $data
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof CommentResource;
    }

    /**
     * @param CommentResource $data
     *
     * @throws \Exception
     */
    public function persist($data, array $context = []): CommentResource
    {
        /** @var User $user */
        $user = $this->security->getUser();
        $groups = [];
        if (!$user instanceof User) {
            $groups = ['anonymous'];
        }
        $this->validator->validate($data, ['groups' => $groups]);

        $comment = $this->decoratedDataPersister->persist($this->createOrUpdateEntity($data));

        if (($context['item_operation_name'] ?? null) === 'post') {
            $this->dispatcher->dispatch(new CommentCreatedEvent($comment));
        }

        return CommentResource::fromComment($comment);
    }

    /**
     * @param CommentResource $data
     */
    public function remove($data, array $context = [])
    {
        if (null === $data->id) {
            return $data;
        }

        $comment = $this->entityManager->getReference(Comment::class, $data->id);

        return $this->decoratedDataPersister->remove($comment);
    }

    private function createOrUpdateEntity(?CommentResource $commentResource): Comment
    {
        if (!$commentResource) {
            $comment = new Comment();
        } else {
            $comment = $this->entityManager->getReference(Comment::class, $commentResource->id);
        }

        /** @var Content $target */
        $target = $this->entityManager->getRepository(Content::class)->find($commentResource->target);
        /** @var Comment|null $parent */
        $parent = $commentResource->parent ? $this->entityManager->getReference(Comment::class, $commentResource->parent) : null;

        $comment
            ->setAuthor($this->auth->getUserOrNull())
            ->setUsername($commentResource->username)
            ->setCreatedAt($commentResource->createdAt)
            ->setContent($commentResource->content)
            ->setParent($parent)
            ->setTarget($target)
        ;

        return $comment;
    }
}
