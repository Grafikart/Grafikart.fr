<?php

namespace App\Core\Twig;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\Blog\Post;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigUrlExtension extends AbstractExtension
{

    private UrlGeneratorInterface $urlGenerator;
    private SerializerInterface $serializer;

    public function __construct(UrlGeneratorInterface $urlGenerator, SerializerInterface $serializer)
    {
        $this->urlGenerator = $urlGenerator;
        $this->serializer = $serializer;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('content_path', [$this, 'contentPath']),
            new TwigFunction('path_for', [$this, 'pathFor'])
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('avatar', [$this, 'avatarPath'])
        ];
    }

    public function contentPath(Content $content): ?string
    {
        if ($content instanceof Post) {
            return $this->urlGenerator->generate('blog_show', ['slug' => $content->getSlug()]);
        }
        return null;
    }

    public function avatarPath(User $user): ?string
    {
        return '/images/default.png';
    }

    public function pathFor (object $object): string {
        return $this->serializer->serialize($object, 'path');
    }

}
