<?php

namespace App\Infrastructure\Image;

use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

/**
 * Renvoie une URL redimensionnée pour une image donnée.
 */
class ImageResizer
{
    private UploaderHelper $helper;

    public function __construct(UploaderHelper $helper)
    {
        $this->helper = $helper;
    }

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
