<?php

namespace App\Infrastructure\Faker;

use App\Domain\Auth\User;
use Faker\Generator;
use Faker\Provider\Base as BaseProvider;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Permet de générer un mot de passe encodé pour les fixtures.
 */
final class PasswordProvider extends BaseProvider
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(Generator $generator, UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        parent::__construct($generator);
    }

    public function password(string $plainPassword): string
    {
        return $this->encoder->encodePassword(new User(), $plainPassword);
    }
}
