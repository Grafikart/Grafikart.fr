<?php

namespace App\Infrastructure\Image;

use League\Glide\Urls\UrlBuilderFactory;

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

    public function __construct(string $signKey)
    {
        $this->signKey = $signKey;
    }

    public function resize(?string $url, ?int $width = null, ?int $height = null): string
    {
        $base = '/resize';
        if (null === $url || empty($url)) {
            return '';
        }
        if (null === $width && null === $height) {
            $url = '/resize/jpg'.$url;
        } else {
            $url = "{$base}/{$width}/{$height}{$url}";
        }
        $urlBuilder = UrlBuilderFactory::create('/', $this->signKey);

        return $urlBuilder->getUrl($url);
    }
}
