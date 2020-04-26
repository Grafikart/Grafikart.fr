<?php

namespace App\Domain\Profile\Exception;

use App\Domain\Profile\Entity\EmailVerification;

class TooManyEmailChangeException extends \Exception
{

    public EmailVerification $emailVerification;

    public function __construct(EmailVerification $emailVerification)
    {
        parent::__construct();
        $this->emailVerification = $emailVerification;
    }

}
