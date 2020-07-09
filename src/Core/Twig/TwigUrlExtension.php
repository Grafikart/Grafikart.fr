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
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TwigUrlExtension extends AbstractExtension
{
    private UrlGeneratorInterface $urlGenerator;
    private SerializerInterface $serializer;
    private UploaderHelper $uploaderHelper;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        UploaderHelper $uploaderHelper,
        SerializerInterface $serializer)
    {
        $this->urlGenerator = $urlGenerator;
        $this->serializer = $serializer;
        $this->uploaderHelper = $uploaderHelper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('content_path', [$this, 'contentPath']),
            new TwigFunction('path', [$this, 'pathFor']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('avatar', [$this, 'avatarPath']),
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
        if (null === $user->getAvatarName()) {
            return '/images/default.png';
        }

        return $this->uploaderHelper->asset($user, 'avatarFile');
    }

    /**
     * @param string|object $path
     */
    public function pathFor($path, array $params = []): string
    {
        if (is_string($path)) {
            return $this->urlGenerator->generate($path, $params);
        }

        return $this->serializer->serialize($path, 'path');
    }
}
