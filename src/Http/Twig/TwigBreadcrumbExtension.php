<?php

namespace App\Http\Twig;

use App\Normalizer\Breadcrumb\BreadcrumbGeneratorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigBreadcrumbExtension extends AbstractExtension
{
    /**
     * @var BreadcrumbGeneratorInterface[]
     */
    private iterable $breadcrumbsGenerator;

    public function __construct(iterable $breadcrumbsGenerator)
    {
        $this->breadcrumbsGenerator = $breadcrumbsGenerator;
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
