<?php

namespace App\Http\Api\Resource;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Comment\Comment;
use App\Domain\Comment\CommentData;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     shortName="Comment",
 *     normalizationContext={"groups"={"read"}},
 *     denormalizationContext={"groups"={"write"}},
 *     itemOperations={
 *         "get"={
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
 * @ORM\Entity(repositoryClass="App\Domain\Comment\CommentRepository")
 */
class CommentResource extends CommentData
{
    /**
     * @Groups({"read"})
     * @ApiProperty(identifier=true)
     */
    private ?int $id = null;

    /**
     * @Groups({"read", "write"})
     * @Assert\NotBlank(groups={"anonymous"}, normalizer="trim")
     */
    private ?string $username = null;

    /**
     * @Assert\NotBlank(normalizer="trim")
     * @Groups({"read", "write"})
     * @Assert\Length(min="4", normalizer="trim")
     */
    private string $content = '';

    /**
     * @Groups({"read"})
     */
    private string $html = '';

    /**
     * @Groups({"read"})
     */
    private ?string $avatar = null;

    /**
     * @Groups({"write"})
     */
    private ?int $target = null;

    /**
     * @Assert\NotBlank(groups={"anonymous"})
     * @Groups({"write"})
     * @Assert\Email(groups={"anonymous"})
     */
    private ?string $email = null;

    /**
     * @Groups({"read"})
     */
    private int $createdAt = 0;

    /**
     * @Groups({"read", "write"})
     */
    private ?int $parent = 0;

    /**
     * Garde une trace de l'entité qui a servi à créer la resource.
     */
    private ?Comment $entity = null;

    /**
     * @Groups({"read"})
     */
    private ?int $userId = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     *
     * @return CommentResource
     */
    public function setId(?int $id): CommentResource
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUsername(): ?string
    {
        return $this->username;
    }

    /**
     * @param string|null $username
     *
     * @return CommentResource
     */
    public function setUsername(?string $username): CommentResource
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return CommentResource
     */
    public function setContent(string $content): CommentResource
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getHtml(): string
    {
        return $this->html;
    }

    /**
     * @param string $html
     *
     * @return CommentResource
     */
    public function setHtml(string $html): CommentResource
    {
        $this->html = $html;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    /**
     * @param string|null $avatar
     *
     * @return CommentResource
     */
    public function setAvatar(?string $avatar): CommentResource
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getTarget(): ?int
    {
        return $this->target;
    }

    /**
     * @param int|null $target
     *
     * @return CommentResource
     */
    public function setTarget(?int $target): CommentResource
    {
        $this->target = $target;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     *
     * @return CommentResource
     */
    public function setEmail(?string $email): CommentResource
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * @param int $createdAt
     *
     * @return CommentResource
     */
    public function setCreatedAt(int $createdAt): CommentResource
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getParent(): ?int
    {
        return $this->parent;
    }

    /**
     * @param int|null $parent
     *
     * @return CommentResource
     */
    public function setParent(?int $parent): CommentResource
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * @return Comment|null
     */
    public function getEntity(): ?Comment
    {
        return $this->entity;
    }

    /**
     * @param Comment|null $entity
     *
     * @return CommentResource
     */
    public function setEntity(?Comment $entity): CommentResource
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @param int|null $userId
     *
     * @return CommentResource
     */
    public function setUserId(?int $userId): CommentResource
    {
        $this->userId = $userId;

        return $this;
    }
}
