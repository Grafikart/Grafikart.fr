<?php

namespace App\Http\Twig;

interface ViewRendererInterface
{

    public function render(string $view, array $context = []): string;

}
