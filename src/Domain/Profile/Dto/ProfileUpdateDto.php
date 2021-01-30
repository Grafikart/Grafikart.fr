<?php

declare(strict_types=1);

namespace App\Domain\Profile\Dto;

use App\Domain\Auth\User;
use App\Validator\Unique;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Données pour la mise à jour du profil utilisateur.
 *
 * @Unique(entityClass="App\Domain\Auth\User", field="email")
 * @Unique(entityClass="App\Domain\Auth\User", field="username")
 */
class ProfileUpdateDto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=100)
     * @Assert\Email()
     */
    public string $email;

    /**
     * @Assert\NotBlank(normalizer="trim")
     * @Assert\Length(min=3, max=40)
     */
    public string $username = '';

    /**
     * @Assert\NotBlank()
     * @Assert\Country()
     */
    public ?string $country = 'FR';

    public User $user;
    public bool $forumNotification;
    public bool $useSystemTheme;
    public bool $useDarkTheme;

    public function __construct(User $user)
    {
        $this->email = $user->getEmail();
        $this->country = $user->getCountry();
        $this->username = $user->getUsername();
        $this->user = $user;
        $this->forumNotification = $user->hasForumMailNotification();
        $this->useSystemTheme = null === $user->getTheme();
        $this->useDarkTheme = 'dark' === $user->getTheme();
    }

    public function getId(): int
    {
        return $this->user->getId() ?: 0;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username ?: '';

        return $this;
    }
}
