<?php

namespace App\Domain\Revision;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\Revision\Constraint\NotSameContent;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Revision\RevisionRepository")
 * @NotSameContent()
 */
class Revision
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Auth\User")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private User $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Application\Entity\Content")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Content $target;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank()
     */
    private string $content = '';

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $createdAt = null;

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
}
