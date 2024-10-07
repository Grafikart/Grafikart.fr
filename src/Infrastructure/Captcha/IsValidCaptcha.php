<?php

namespace App\Infrastructure\Captcha;

use Symfony\Component\Validator\Constraint;

/**
 * Vérifie la validité du captcha.
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE)]
class IsValidCaptcha extends Constraint
{
    public string $message = "Le captcha n'est pas valide";
}
