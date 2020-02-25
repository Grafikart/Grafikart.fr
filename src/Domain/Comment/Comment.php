<?php

namespace App\Domain\Comment;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Comment\CommentRepository")
 */
class Comment
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $username = null;

    /**
     * @ORM\Column(type="text")
     */
    private string $content = '';

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Auth\User")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=true)
     */
    private ?User $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Comment\Comment")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?self $parent;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Application\Entity\Content")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false, name="content_id")
     */
    private Content $target;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): Comment
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail(): ?string
    {
        if ($this->author !== null) {
            return $this->author->getEmail();
        }
        return $this->email;
    }

    public function setEmail(?string $email): Comment
    {
        $this->email = $email;
        return $this;
    }

    public function getUsername(): ?string
    {
        if ($this->author !== null) {
            return $this->author->getUsername();
        }
        return $this->username;
    }

    public function setUsername(?string $username): Comment
    {
        $this->username = $username;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): Comment
    {
        $this->content = $content;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): Comment
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): Comment
    {
        $this->author = $author;
        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): Comment
    {
        $this->parent = $parent;
        return $this;
    }

    public function getTarget(): ?Content
    {
        return $this->target;
    }

    public function setTarget(Content $target): self
    {
        $this->target = $target;

        return $this;
    }


}
