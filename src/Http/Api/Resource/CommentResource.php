<?php

namespace App\Http\Api\Resource;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Domain\Auth\User;
use App\Domain\Comment\CommentData;
use App\Domain\Comment\Entity\Comment;
use App\Http\Api\DataProvider\CommentApiProvider;
use App\Http\Api\Processor\CommentProcessor;
use App\Http\Security\CommentVoter;
use App\Validator\NotExists;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[ApiResource(
    shortName: 'Comment',
    operations: [
        new GetCollection(),
        new Post(processor: CommentProcessor::class),
        new Delete(security: "is_granted('".CommentVoter::DELETE."' , object)", processor: CommentProcessor::class),
        new Put(security: "is_granted('".CommentVoter::UPDATE."', object)", processor: CommentProcessor::class),
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
    provider: CommentApiProvider::class,
)]
class CommentResource extends CommentData
{
    #[Groups(['read'])]
    #[ApiProperty(identifier: true)]
    public ?int $id = null;

    #[NotExists(class: User::class, groups: ['anonymous'], field: 'username', message: 'Ce pseudo est utilisé par un utilisateur')]
    #[Groups(['read', 'write'])]
    #[Assert\NotBlank(normalizer: 'trim', groups: ['anonymous'])]
    public ?string $username = null;

    #[Assert\NotBlank(normalizer: 'trim')]
    #[Groups(['read', 'write'])]
    #[Assert\Length(min: 4, max: 1_000, normalizer: 'trim')]
    public string $content = '';

    #[Groups(['read'])]
    public string $html = '';

    #[Groups(['read'])]
    public ?string $avatar = null;

    #[Groups(['write'])]
    public ?int $target = null;

    #[Groups(['read'])]
    public int $createdAt = 0;

    #[Groups(['read', 'write'])]
    public ?int $parent = 0;

    /**
     * Garde une trace de l'entité qui a servi à créer la resource.
     */
    public ?Comment $entity = null;

    #[Groups(['read'])]
    public ?int $userId = null;

    public static function fromComment(Comment $comment, ?UploaderHelper $uploaderHelper = null): CommentResource
    {
        $resource = new self();
        $author = $comment->getAuthor();
        $resource->id = $comment->getId();
        $resource->username = $comment->getUsername();
        $resource->content = $comment->getContent();
        $resource->html = strip_tags(
            (string) (new \Parsedown())
                ->setBreaksEnabled(true)
                ->setSafeMode(true)
                ->text($comment->getContent()),
            '<p><pre><code><ul><ol><li>'
        );
        $resource->createdAt = $comment->getCreatedAt()->getTimestamp();
        $resource->parent = null !== $comment->getParent() ? $comment->getParent()->getId() : 0;
        if ($author && $uploaderHelper && $author->getAvatarName()) {
            $resource->avatar = $uploaderHelper->asset($author, 'avatarFile');
        } else {
            $resource->avatar = '/images/default.png';
        }
        $resource->entity = $comment;
        $resource->userId = $author ? $author->getId() : null;

        return $resource;
    }
}
