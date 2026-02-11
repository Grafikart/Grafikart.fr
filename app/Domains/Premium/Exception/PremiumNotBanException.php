<?php

namespace App\Domains\Premium\Exception;

class PremiumNotBanException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Impossible de bannir un utilisateur premium', 0, null);
    }
}
