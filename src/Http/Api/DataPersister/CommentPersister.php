<?php

namespace App\Http\Api\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\Comment\Comment;
use App\Domain\Comment\CommentService;
use App\Http\Api\Resource\CommentResource;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class CommentPersister implements ContextAwareDataPersisterInterface
{
    private ValidatorInterface $validator;
    private Security $security;
    /**
     * @var CommentService
     */
    private CommentService $service;

    public function __construct(
        ValidatorInterface $validator,
        Security $security,
        CommentService $service
    ) {
        $this->validator = $validator;
        $this->security = $security;
        $this->service = $service;
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

        if (null !== $data->entity) {
            $comment = $this->service->update($data->entity, $data->content);
        } else {
            $comment = $this->service->create($data);
        }

        return CommentResource::fromComment($comment);
    }

    /**
     * @param CommentResource $data
     */
    public function remove($data, array $context = []): CommentResource
    {
        if ($data->id === null) {
            return $data;
        }
        $this->service->delete($data->id);
        return $data;
    }
}
