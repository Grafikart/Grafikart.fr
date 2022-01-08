<?php

namespace App\Normalizer\Breadcrumb;

class BreadcrumbItem
{
    public function __construct(public string $title, public array $path)
    {
    }
}
