<?php

namespace App\Domain\Notification\Entity;

use App\Domain\Auth\User;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Notification\Repository\NotificationRepository")
 */
class Notification
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Auth\User")
     * @ORM\JoinColumn(name="user_id", onDelete="CASCADE", nullable=true)
     */
    private ?User $user = null;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     * @Groups({"read:notification", "create:notification"})
     */
    private string $message;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Url()
     * @Groups({"read:notification", "create:notification"})
     */
    private ?string $url = null;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read:notification"})
     */
    private \DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"create:notification"})
     */
    private ?string $channel = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $target = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Notification
    {
        $this->id = $id;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): Notification
    {
        $this->user = $user;
        return $this;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function setMessage(string $message): Notification
    {
        $this->message = $message;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): Notification
    {
        $this->url = $url;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): Notification
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(?string $channel): Notification
    {
        $this->channel = $channel;
        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(?string $target): Notification
    {
        $this->target = $target;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRead(): bool
    {
        if ($this->user === null) {
            return false;
        }
        $notificationsReadAt = $this->user->getNotificationsReadAt();
        return $notificationsReadAt ? ($this->createdAt > $notificationsReadAt) : false;
    }

}
