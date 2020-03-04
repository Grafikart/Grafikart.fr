<?php

namespace App\Twig;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Domain\Application\Entity\Content;
use App\Domain\Blog\Post;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigUrlExtension extends AbstractExtension
{

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('content_path', [$this, 'contentPath'])
        ];
    }

    public function contentPath(Content $content): ?string
    {
        if ($content instanceof Post) {
            return $this->urlGenerator->generate('blog_show', ['slug' => $content->getSlug()]);
        }
        return null;
    }

}
