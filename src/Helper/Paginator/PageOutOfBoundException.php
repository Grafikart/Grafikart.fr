<?php

namespace App\Helper\Paginator;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PageOutOfBoundException extends BadRequestHttpException
{
}
