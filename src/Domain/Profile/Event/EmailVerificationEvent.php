<?php

namespace App\Domain\Profile\Event;

use App\Domain\Profile\Entity\EmailVerification;

class EmailVerificationEvent
{
    public function __construct(public EmailVerification $emailVerification)
    {
    }
}
