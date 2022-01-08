<?php

namespace App\Http\Twig;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigBreadcrumbExtension extends AbstractExtension
{
    public function __construct(private readonly iterable $breadcrumbsGenerator)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('breadcrumb', [$this, 'breadcrumb'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function breadcrumb(Environment $env, object $object): string
    {
        $items = [];
        foreach ($this->breadcrumbsGenerator as $generator) {
            if ($generator->support($object)) {
                $items = $generator->generate($object);
            }
        }

        return $env->render('partials/breadcrumb.html.twig', [
            'items' => $items,
        ]);
    }
}
