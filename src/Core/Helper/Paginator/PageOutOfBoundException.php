<?php

namespace App\Core\Helper\Paginator;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class PageOutOfBoundException extends BadRequestHttpException
{
}
