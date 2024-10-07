<?php

namespace App\Domain\Forum\Entity;

use App\Domain\Auth\User;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'forum_read_time')]
#[ORM\Entity(repositoryClass: \App\Domain\Forum\Repository\ReadTimeRepository::class)]
class ReadTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue()]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Topic::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Topic $topic;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $owner;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeInterface $readAt;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $notified = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): ReadTime
    {
        $this->id = $id;

        return $this;
    }

    public function getTopic(): Topic
    {
        return $this->topic;
    }

    public function setTopic(Topic $topic): ReadTime
    {
        $this->topic = $topic;

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): ReadTime
    {
        $this->owner = $owner;

        return $this;
    }

    public function getReadAt(): \DateTimeInterface
    {
        return $this->readAt;
    }

    public function setReadAt(\DateTimeInterface $readAt): ReadTime
    {
        $this->readAt = $readAt;

        return $this;
    }

    public function isNotified(): bool
    {
        return $this->notified;
    }

    public function setNotified(bool $notified): ReadTime
    {
        $this->notified = $notified;

        return $this;
    }
}
