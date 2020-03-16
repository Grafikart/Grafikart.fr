<?php

namespace App\Core\Twig;

interface BreadcrumbInterface
{

    public function generate ($course): array;

    public function support ($object): bool;

}
