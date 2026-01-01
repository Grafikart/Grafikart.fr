<?php

namespace App\Component\ObjectMapper\Transform;

use App\Component\ObjectMapper\TransformCallableInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Transform an object into a URL path
 */
readonly class UrlTransformer implements TransformCallableInterface
{

    public function __construct(private SerializerInterface $serializer){
    }

    public function __invoke(mixed $value, object $source, ?object $target): string
    {
        return $this->serializer->serialize($source, 'path', ['url' => false]);
    }
}
