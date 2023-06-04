<?php

namespace App\Infrastructure\Captcha\HCaptcha;

use Symfony\Component\Validator\Constraint;

/**
 * Check if the captcha is valid.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class IsValidHCaptcha extends Constraint
{
    public string $message = "Le captcha n'est pas valide";
}
