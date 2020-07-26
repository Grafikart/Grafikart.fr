<?php

namespace App\Domain\Auth;

use App\Domain\Forum\Entity\ForumReaderUserInterface;
use App\Domain\Notification\Entity\Notifiable;
use App\Domain\Premium\Entity\PremiumTrait;
use App\Infrastructure\Payment\Stripe\StripeEntity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Domain\Auth\UserRepository")
 * @ORM\Table(name="`user`")
 * @Vich\Uploadable()
 */
class User implements UserInterface, \Serializable, ForumReaderUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $username = '';

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private string $email = '';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $password = '';

    /** @var array<string> */
    private array $roles = ['ROLE_USER'];

    /**
     * @Vich\UploadableField(mapping="avatars", fileNameProperty="avatarName")
     */
    private ?File $avatarFile = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $avatarName = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $updatedAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $createdAt = null;

    /**
     * @ORM\Column(type="string", length=2, nullable=true)
     */
    private ?string $country = null;

    /**
     * Date de dernière lecture du forum.
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $forumReadTime = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTimeInterface $bannedAt = null;

    use PremiumTrait;
    use StripeEntity;
    use Notifiable;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials(): void
    {
    }

    public function getAvatarFile(): ?File
    {
        return $this->avatarFile;
    }

    public function setAvatarFile(?File $avatarFile): User
    {
        $this->avatarFile = $avatarFile;

        return $this;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    public function setAvatarName(?string $avatarName): User
    {
        $this->avatarName = $avatarName;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function serialize(): string
    {
        return serialize([
            $this->id,
            $this->username,
            $this->password,
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized): void
    {
        [
            $this->id,
            $this->username,
            $this->password,
        ] = unserialize($serialized);
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): User
    {
        $this->country = $country;

        return $this;
    }

    public function getForumReadTime(): ?\DateTimeInterface
    {
        return $this->forumReadTime;
    }

    public function setForumReadTime(?\DateTimeInterface $forumReadTime): User
    {
        $this->forumReadTime = $forumReadTime;

        return $this;
    }

    public function getBannedAt(): ?\DateTimeInterface
    {
        return $this->bannedAt;
    }

    public function setBannedAt(?\DateTimeInterface $bannedAt): User
    {
        $this->bannedAt = $bannedAt;

        return $this;
    }

    public function isBanned(): bool
    {
        return null !== $this->bannedAt;
    }
}
