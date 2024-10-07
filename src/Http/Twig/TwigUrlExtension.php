<?php

namespace App\Http\Twig;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\Blog\Post;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Vich\UploaderBundle\Templating\Helper\UploaderHelperInterface;

class TwigUrlExtension extends AbstractExtension
{
    public function __construct(
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly UploaderHelperInterface $uploaderHelper,
        private readonly SerializerInterface $serializer,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('content_path', $this->contentPath(...)),
            new TwigFunction('path', $this->pathFor(...)),
            new TwigFunction('url', $this->urlFor(...)),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('avatar', $this->avatarPath(...)),
            new TwigFilter('autolink', $this->autoLink(...)),
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

        return sprintf(
            '%s?uid=%s',
            $this->uploaderHelper->asset($user, 'avatarFile'),
            $user->getUpdatedAt()?->getTimestamp() ?: 0
        );
    }

    /**
     * @param string|object $path
     */
    public function pathFor($path, array $params = []): string
    {
        if (is_string($path)) {
            return $this->urlGenerator->generate($path, $params);
        }

        return $this->serializer->serialize($path, 'path', ['url' => false]);
    }

    /**
     * @param string|object $path
     */
    public function urlFor($path, array $params = []): string
    {
        if (is_string($path)) {
            return $this->urlGenerator->generate(
                $path,
                $params,
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        }

        return $this->serializer->serialize($path, 'path', ['url' => true]);
    }

    public function autoLink(string $string): string
    {
        $regexp = '/(<a.*?>)?(https?:)?(\/\/)(\w+\.)?(\w+\.[\w\/\-_.~&=?]+)(<\/a>)?/i';
        $anchor = '<a href="%s//%s" target="_blank" rel="noopener noreferrer">%s</a>';

        preg_match_all($regexp, $string, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            if (empty($match[1]) && empty($match[6])) {
                $protocol = $match[2] ?: 'https:';
                $replace = sprintf($anchor, $protocol, $match[5], $match[0]);
                $string = str_replace($match[0], $replace, $string);
            }
        }

        return $string;
    }
}
