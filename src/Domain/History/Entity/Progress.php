<?php

namespace App\Domain\History\Entity;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="progress_unique",
 *            columns={"author_id", "content_id"})
 *    }
 * )
 * @ORM\Entity(repositoryClass="App\Domain\History\Repository\ProgressRepository")
 */
class Progress
{
    public const TOTAL = 1000;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
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
    private Content $content;

    /**
     * @ORM\Column(type="integer")
     */
    private int $progress = 0;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?\DateTimeInterface $updatedAt = null;

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

    public function getContent(): Content
    {
        return $this->content;
    }

    public function setContent(Content $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getRatio(): ?float
    {
        return $this->progress / self::TOTAL;
    }

    public function getProgress(): ?int
    {
        return $this->progress;
    }

    public function setProgress(int $progress): self
    {
        $this->progress = $progress;

        return $this;
    }

    public function isFinished(): bool
    {
        return self::TOTAL === $this->progress;
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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function setRatio(float $ratio): self
    {
        $this->progress = (int) floor($ratio * self::TOTAL);

        return $this;
    }
}
