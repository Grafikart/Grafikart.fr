<?php

namespace App\Domain\Forum\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use App\Domain\Auth\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Forum\Repository\ReportRepository")
 * @ORM\Table(name="forum_report")
 * @Assert\Expression(
 *     expression="this.getMessage() !== null || this.getTopic() !== null",
 *     message="Un signalement doit être associé à un topic ou un message"
 * )
 */
class Report
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     * @ApiProperty(identifier=true)
     * @Groups({"read:report"})
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Auth\User")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private User $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Forum\Entity\Topic")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     * @Groups({"create:report"})
     */
    private ?Topic $topic = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Forum\Entity\Message")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     * @Groups({"create:report"})
     */
    private ?Message $message = null;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(min="4")
     * @Groups({"create:report", "read:report"})
     */
    private string $reason;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Report
    {
        $this->id = $id;

        return $this;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): Report
    {
        $this->author = $author;

        return $this;
    }

    public function getTopic(): ?Topic
    {
        return $this->topic;
    }

    public function setTopic(?Topic $topic): Report
    {
        $this->topic = $topic;

        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): Report
    {
        $this->message = $message;

        return $this;
    }

    /**
     * @return Message|Topic
     */
    public function getTarget()
    {
        return $this->message ?: $this->topic;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): Report
    {
        $this->reason = $reason;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): Report
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
