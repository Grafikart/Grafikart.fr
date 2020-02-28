<?php

namespace App\Http\Api\Resource;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Comment\Comment;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     shortName="Comment",
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}},
 *     itemOperations={
"get"={
 *             "controller"=NotFoundAction::class,
 *             "read"=false,
 *             "output"=false,
 *         },
 *         "delete"={
 *             "security"="is_granted(constant('App\\Http\\Security\\CommentVoter::DELETE'), object)"
 *         },
 *         "put"={
 *             "security"="is_granted(constant('App\\Http\\Security\\CommentVoter::UPDATE'), object)"
 *         }
 *     }
 * )
 */
class CommentResource
{

    /**
     * @Groups({"read"})
     * @ApiProperty(identifier=true)
     */
    public ?int $id = null;

    /**
     * @Groups({"read", "write"})
     * @Assert\NotBlank(groups={"anonymous"})
     */
    public ?string $username = null;

    /**
     * @Assert\NotBlank()
     * @Groups({"read", "write"})
     * @Assert\Length(min="4")
     */
    public string $content = '';

    /**
     * @Groups({"read"})
     */
    public ?string $avatar = null;

    /**
     * @Groups({"write"})
     */
    public ?int $target = null;

    /**
     * @Assert\NotBlank(groups={"anonymous"})
     * @Groups({"write"})
     * @Assert\Email(groups={"anonymous"})
     */
    public ?string $email = null;

    /**
     * @Groups({"read"})
     */
    public int $createdAt = 0;

    /**
     * @Groups({"read", "write"})
     */
    public ?int $parent = 0;

    /**
     * Garde une trace de l'entité qui a servi à créer la resource
     */
    public ?Comment $entity = null;

    static public function fromComment(Comment $comment): CommentResource
    {
        $resource = new self();
        $resource->id = $comment->getId();
        $resource->username = $comment->getUsername();
        $resource->content = $comment->getContent();
        $resource->createdAt = $comment->getCreatedAt()->getTimestamp();
        $resource->parent = $comment->getParent() !== null ? $comment->getParent()->getId() : null;
        $gravatar = md5($comment->getEmail());
        $resource->avatar = "https://1.gravatar.com/avatar/{$gravatar}?s=200&r=pg&d=mp";
        $resource->entity = $comment;
        return $resource;
    }

}
