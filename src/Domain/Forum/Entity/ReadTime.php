<?php

namespace App\Domain\Forum\Entity;

use App\Domain\Auth\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Forum\Repository\ReadTimeRepository")
 * @ORM\Table(name="forum_read_time")
 */
class ReadTime
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Forum\Entity\Topic")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Topic $topic;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Auth\User")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private User $owner;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $readAt;

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

}
