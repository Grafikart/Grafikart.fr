<?php

namespace App\Domain\Password\Entity;

use App\Domain\Auth\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Password\Repository\PasswordResetTokenRepository")
 */
class PasswordResetToken
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    public ?int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    public \DateTime $createdAt;

    /**
     * @ORM\Column(type="string")
     */
    public string $token;

    /**
     * @ORM\OneToOne(targetEntity="App\Domain\Auth\User")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    public User $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
