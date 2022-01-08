<?php

namespace App\Domain\Profile\Exception;

use App\Domain\Profile\Entity\EmailVerification;

class TooManyEmailChangeException extends \Exception
{
    public function __construct(public EmailVerification $emailVerification)
    {
        parent::__construct();
    }
}
