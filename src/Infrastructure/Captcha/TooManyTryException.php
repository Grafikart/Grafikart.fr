<?php

namespace App\Infrastructure\Captcha;

use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\HttpFoundation\Response;

#[WithHttpStatus(Response::HTTP_FORBIDDEN)]
class TooManyTryException extends \Exception
{

    protected $message = "Trop d'essais";

}
