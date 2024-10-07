<?php

namespace App\Infrastructure\Captcha;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;

#[WithHttpStatus(Response::HTTP_FORBIDDEN)]
class TooManyTryException extends \Exception
{
    /** @var string */
    protected $message = "Trop d'essais";
}
