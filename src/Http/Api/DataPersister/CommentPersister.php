<?php

namespace App\Http\Api\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\Comment\Comment;
use App\Http\Api\Resource\CommentResource;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class CommentPersister implements ContextAwareDataPersisterInterface
{
    private ValidatorInterface $validator;
    private Security $security;
    private EntityManagerInterface $em;
    private UploaderHelper $uploader;

    public function __construct(
        ValidatorInterface $validator,
        Security $security,
        EntityManagerInterface $em,
        UploaderHelper $uploader
    ) {
        $this->validator = $validator;
        $this->security = $security;
        $this->em = $em;
        $this->uploader = $uploader;
    }

    /**
     * @param object $data
     * @param array  $context
     *
     * @return bool
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof CommentResource;
    }

    /**
     * @param CommentResource $data
     * @param array           $context
     *
     * @return CommentResource
     * @throws \Doctrine\ORM\ORMException
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
            // On met à jour un commentaire
            $comment = $data->entity;
            $comment->setContent($data->content);
        } else {
            // On crée un nouveau commentaire
            /** @var Content $target */
            $target = $this->em->getRepository(Content::class)->find($data->target);
            /** @var Comment|null $parent */
            $parent = $data->parent ? $this->em->getReference(Comment::class, $data->parent) : null;
            $comment = (new Comment())
                ->setAuthor($user)
                ->setUsername($data->username)
                ->setEmail($data->email)
                ->setCreatedAt(new \DateTime())
                ->setContent($data->content)
                ->setParent($parent)
                ->setTarget($target);
            $this->em->persist($comment);
        }
        $this->em->flush();

        return CommentResource::fromComment($comment, $this->uploader);
    }

    /**
     * @param CommentResource $data
     */
    public function remove($data, array $context = []): CommentResource
    {
        /** @var Comment $comment */
        $comment = $this->em->getReference(Comment::class, $data->id);
        $this->em->remove($comment);
        $this->em->flush();

        return $data;
    }
}
