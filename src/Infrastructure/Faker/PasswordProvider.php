<?php

namespace App\Infrastructure\Faker;

use App\Domain\Auth\User;
use Faker\Generator;
use Faker\Provider\Base as BaseProvider;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Permet de générer un mot de passe encodé pour les fixtures.
 */
final class PasswordProvider extends BaseProvider
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(Generator $generator, UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
        parent::__construct($generator);
    }

    public function password(string $plainPassword): string
    {
        return $this->hasher->hashPassword(new User(), $plainPassword);
    }
}
