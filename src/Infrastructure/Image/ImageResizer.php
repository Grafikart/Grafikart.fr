<?php

namespace App\Infrastructure\Image;

use League\Glide\Urls\UrlBuilderFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Renvoie une URL redimensionnée pour une image donnée.
 */
class ImageResizer
{
    /**
     * Clef permettant de signer les URLs pour le redimensionnement
     * (cf https://glide.thephpleague.com/1.0/config/security/).
     */
    private string $signKey;

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(string $signKey, UrlGeneratorInterface $urlGenerator)
    {
        $this->signKey = $signKey;
        $this->urlGenerator = $urlGenerator;
    }

    public function resize(?string $url, ?int $width = null, ?int $height = null): string
    {
        if (null === $url || empty($url)) {
            return '';
        }
        if (null === $width && null === $height) {
            $url = $this->urlGenerator->generate('image_jpg', ['path' => trim($url, '/')]);
        } else {
            $url = $this->urlGenerator->generate('image_resizer', ['path' => trim($url, '/'), 'width' => $width, 'height' => $height]);
        }
        $urlBuilder = UrlBuilderFactory::create('/', $this->signKey);

        return $urlBuilder->getUrl($url);
    }
}
