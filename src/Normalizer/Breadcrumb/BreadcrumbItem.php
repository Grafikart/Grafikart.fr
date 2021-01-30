<?php

namespace App\Normalizer\Breadcrumb;

class BreadcrumbItem
{
    public string $title;
    public array $path;

    public function __construct(string $title, array $path)
    {
        $this->title = $title;
        $this->path = $path;
    }
}
