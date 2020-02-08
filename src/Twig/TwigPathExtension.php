<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigPathExtension extends AbstractExtension
{



    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploads_path', [$this, 'uploadsPath']),
        ];
    }

    public function uploadsPath(string $path): string
    {
        return '/uploads/' . trim($path, '/');
    }
}
