<?php

namespace App\Core\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigBreadcrumbExtension extends AbstractExtension
{

    private UrlGeneratorInterface $urlGenerator;

    /**
     * @var BreadcrumbInterface[]
     */
    private iterable $breadcrumbsGenerator;

    public function __construct(iterable $breadcrumbsGenerator, UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->breadcrumbsGenerator = $breadcrumbsGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('breadcrumb', [$this, 'breadcrumb'], [
                'needs_environment' => true,
                'is_safe'           => ['html']
            ])
        ];
    }

    public function breadcrumb(Environment $env, object $object): string
    {
        $items = [];
        foreach ($this->breadcrumbsGenerator as $generator) {
            if ($generator->support($object)) {
                $items = $generator->generate($object);
                foreach($items as $label => $path) {
                    if (is_array($path)) {
                        $items[$label] = $this->urlGenerator->generate($path[0], $path[1] ?? []);
                    }
                }
            }
        }
        return $env->render('partials/breadcrumb.html.twig', [
            'items' => $items
        ]);
    }

}
