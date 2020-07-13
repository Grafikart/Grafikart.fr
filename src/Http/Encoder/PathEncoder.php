<?php

namespace App\Http\Encoder;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

/**
 * Convertit un tableau (issue du pathNormalizer) en chemin.
 *
 * ## Exemple
 *
 * [
 *      'path' => 'blog_show',
 *      'params' => ['slug' => 'demo', 'id' => 3],
 *      'hash' => 'c3'
 * ]
 */
class PathEncoder implements EncoderInterface
{
    const FORMAT = 'path';

    private UrlGeneratorInterface $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param array $data
     */
    public function encode($data, string $format, array $context = []): string
    {
        ['path' => $path, 'params' => $params] = $data;

        $hash = isset($data['hash']) ? '#'.$data['hash'] : '';

        return $this->urlGenerator->generate($path, $params).$hash;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding(string $format): bool
    {
        return self::FORMAT === $format;
    }
}
