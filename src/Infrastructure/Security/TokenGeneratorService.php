<?php

namespace App\Infrastructure\Security;

class TokenGeneratorService
{
    /**
     * Génère une chaine de caractère aléatoire d'une taille définie.
     */
    public function generate(int $length = 25): string
    {
        /** @var int<1, max> $bytesLength */
        $bytesLength = max((int)ceil($length / 2), 1);
        return substr(bin2hex(random_bytes($bytesLength)), 0, $length);
    }
}
