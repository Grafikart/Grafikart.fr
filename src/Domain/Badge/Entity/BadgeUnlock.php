<?php

namespace App\Domain\Badge\Entity;

use App\Domain\Auth\User;
use App\Domain\Badge\Repository\BadgeUnlockRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BadgeUnlockRepository::class)
 */
class BadgeUnlock
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private User $owner;

    /**
     * @ORM\ManyToOne(targetEntity=Badge::class)
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private Badge $badge;

    /**
     * @ORM\Column(type="datetime")
     */
    private \DateTimeInterface $createdAt;

    public function __construct(User $user, Badge $badge)
    {
        $this->badge = $badge;
        $this->owner = $user;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): BadgeUnlock
    {
        $this->id = $id;

        return $this;
    }

    public function getOwner(): User
    {
        return $this->owner;
    }

    public function setOwner(User $owner): BadgeUnlock
    {
        $this->owner = $owner;

        return $this;
    }

    public function getBadge(): Badge
    {
        return $this->badge;
    }

    public function setBadge(Badge $badge): BadgeUnlock
    {
        $this->badge = $badge;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): BadgeUnlock
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
