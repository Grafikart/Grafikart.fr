<?php

namespace App\Http\Encoder;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use Symfony\Component\Serializer\Encoder\EncoderInterface;

/**
 * Convertit un tableau de taille 2 en chemin
 *
 * ## Exemple
 *
 * ['blog_show', ['slug' => 'demo', 'id' => 3]
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
        return $this->urlGenerator->generate($path, $params);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsEncoding(string $format): bool
    {
        return self::FORMAT === $format;
    }

}
