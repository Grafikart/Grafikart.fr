<?php

namespace App\Http\Twig;

use Twig\Environment;

class TwigViewRenderer implements ViewRendererInterface
{

    public function __construct(private Environment $twig)
    {
    }


    public function render(string $view, array $context = []): string
    {
        return $this->twig->render($view, $context);
    }
}
