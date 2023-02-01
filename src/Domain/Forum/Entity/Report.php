<?php

namespace App\Domain\Forum\Entity;

use ApiPlatform\Metadata\ApiProperty;
use App\Domain\Auth\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'forum_report')]
#[ORM\Entity(repositoryClass: \App\Domain\Forum\Repository\ReportRepository::class)]
#[Assert\Expression(expression: 'this.getMessage() !== null || this.getTopic() !== null', message: 'Un signalement doit être associé à un topic ou un message')]
class Report
{
    #[ApiProperty(identifier: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read:report'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: \App\Domain\Auth\User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $author;

    #[ORM\ManyToOne(targetEntity: \App\Domain\Forum\Entity\Topic::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[Groups(['create:report'])]
    private ?Topic $topic = null;

    #[ORM\ManyToOne(targetEntity: \App\Domain\Forum\Entity\Message::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'CASCADE')]
    #[Groups(['create:report'])]
    private ?Message $message = null;

    #[ORM\Column(type: 'string')]
    #[Groups(['create:report', 'read:report'])]
    #[Assert\Length(min: 3, max: 250)]
    #[Assert\NotBlank(normalizer: 'trim')]
    private string $reason;

    #[ORM\Column(type: 'datetime')]
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

    public function getTarget(): Message|Topic
    {
        if ($this->message) {
            return $this->message;
        } elseif ($this->topic) {
            return $this->topic;
        }
        throw new \RuntimeException("Ce signalement n'est pas rattaché à un contenu");
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
