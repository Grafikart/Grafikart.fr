<?php

namespace App\Http\Api\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Domain\Application\Entity\Content;
use App\Domain\Comment\Comment;
use App\Http\Api\Dto\CommentInput;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

final class CommentInputDataTransformer implements DataTransformerInterface
{

    private EntityManagerInterface $em;
    private Security $security;
    private ValidatorInterface $validator;

    public function __construct(
        Security $security,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    )
    {
        $this->em = $em;
        $this->security = $security;
        $this->validator = $validator;
    }

    /**
     * @param CommentInput $data
     */
    public function transform($data, string $to, array $context = []): Comment
    {
        $user = $this->security->getUser();
        $groups = [];
        if ($user === null) {
            $groups = ['anonymous'];
        }
        $this->validator->validate($data, ['groups' => $groups]);
        $comment = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Comment();
        /** @var Content $target */
        $target = $this->em->getRepository(Content::class)->find($data->target);
        $comment
            ->setAuthor($user)
            ->setUsername($data->username)
            ->setEmail($data->email)
            ->setCreatedAt(new \DateTime())
            ->setContent($data->content)
            ->setTarget($target);
        return $comment;
    }

    /**
     * @inheritDoc
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return Comment::class === $to && null !== ($context['input']['class'] ?? null);
    }
}
