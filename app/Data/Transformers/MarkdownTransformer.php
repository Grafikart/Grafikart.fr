<?php

namespace App\Data\Transformers;

use App\Infrastructure\Blade\Markdown;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

class MarkdownTransformer implements Transformer
{
    public function transform(DataProperty $property, mixed $value, TransformationContext $context): mixed
    {
        if (is_string($value)) {
            return Markdown::html($value);
        }

        return $value;
    }
}
