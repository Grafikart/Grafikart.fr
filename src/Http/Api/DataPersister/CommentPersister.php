<?php

namespace App\Http\Api\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Domain\Auth\User;
use App\Domain\Comment\CommentRepository;
use App\Domain\Comment\CommentService;
use App\Http\Api\Resource\CommentResource;
use Symfony\Component\Security\Core\Security;

class CommentPersister implements ContextAwareDataPersisterInterface
{
    private ValidatorInterface $validator;
    private Security $security;
    private CommentService $service;
    private CommentRepository $commentRepository;

    public function __construct(
        ValidatorInterface $validator,
        Security $security,
        CommentService $service,
        CommentRepository $commentRepository
    ) {
        $this->validator = $validator;
        $this->security = $security;
        $this->service = $service;
        $this->commentRepository = $commentRepository;
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

        if (null !== $data->getEntity()) {
            $comment = $this->service->update($data->getEntity(), $data->getContent());
        } else {
            $comment = $this->service->create($data);
        }

        return $this->commentRepository::fromComment($comment);
    }

    /**
     * @param CommentResource $data
     */
    public function remove($data, array $context = []): CommentResource
    {
        if (null === $data->getId()) {
            return $data;
        }
        $this->service->delete($data->getId());

        return $data;
    }
}
