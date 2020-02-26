<?php

namespace App\Http\Api\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Domain\Comment\Comment;
use App\Http\Api\Dto\CommentOutput;

final class CommentOutputDataTransformer implements DataTransformerInterface
{

    /**
     * @param Comment $object
     */
    public function transform($object, string $to, array $context = []): CommentOutput
    {
        return new CommentOutput($object);
    }

    /**
     * @inheritDoc
     */
    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $data instanceof Comment && $to === CommentOutput::class;
    }
}
