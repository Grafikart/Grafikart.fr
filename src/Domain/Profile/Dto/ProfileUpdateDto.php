<?php

declare(strict_types=1);

namespace App\Domain\Profile\Dto;

use App\Core\Validator\Unique;
use App\Domain\Auth\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Données pour la mise à jour du profil utilisateur.
 *
 * @Unique(entityClass="App\Domain\Auth\User", field="email")
 */
class ProfileUpdateDto
{
    /**
     * @Assert\NotBlank()
     */
    public string $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Country()
     */
    public ?string $country;

    public User $user;

    public function __construct(User $user)
    {
        $this->email = $user->getEmail();
        $this->country = $user->getCountry();
        $this->user = $user;
    }

    public function getId(): int
    {
        return $this->user->getId() ?: 0;
    }
}
