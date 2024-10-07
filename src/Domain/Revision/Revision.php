<?php

namespace App\Domain\Revision;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\Revision\Constraint\NotSameContent;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Domain\Revision\RevisionRepository::class)]
#[NotSameContent]
class Revision
{
    final public const PENDING = 0;
    final public const ACCEPTED = 1;
    final public const REJECTED = -1;

    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: \App\Domain\Auth\User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $author;

    #[ORM\ManyToOne(targetEntity: \App\Domain\Application\Entity\Content::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Content $target;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $content = '';

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'integer', length: 1, options: ['default' => 0])]
    private int $status = self::PENDING;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $comment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getTarget(): Content
    {
        return $this->target;
    }

    public function setTarget(Content $target): self
    {
        $this->target = $target;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): Revision
    {
        $this->status = $status;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): Revision
    {
        $this->comment = $comment;

        return $this;
    }
}
