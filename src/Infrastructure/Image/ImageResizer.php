<?php

namespace App\Infrastructure\Image;

/**
 * Renvoie une URL redimensionnée pour une image donnée.
 */
class ImageResizer
{
    public function resize(?string $url, ?int $width = null, ?int $height = null): string
    {
        if (null === $url || empty($url)) {
            return '';
        }
        if (null === $width && null === $height) {
            return '/resize/jpg'.$url;
        }

        return "/resize/r_{$width}_{$height}{$url}";
    }
}
