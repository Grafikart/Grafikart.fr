<?php

namespace App\Domain\Profile\Event;

use App\Domain\Profile\Entity\EmailVerification;

class EmailVerificationEvent
{

    public EmailVerification $emailVerification;

    public function __construct(EmailVerification $emailVerification)
    {
        $this->emailVerification = $emailVerification;
    }

}
