<?php

namespace App\Infrastructure\Image;

use League\Glide\Urls\UrlBuilderFactory;

class ImageUrlGenerator
{
    public static function resize(string $path, int $width, int $height): string
    {
        $parts = parse_url($path);
        $url = sprintf('/media/resize/%d/%d%s', $width, $height, $parts['path']);
        $urlBuilder = UrlBuilderFactory::create('/', config('image.resize_key'));

        return $urlBuilder->getUrl($url);
    }
}
