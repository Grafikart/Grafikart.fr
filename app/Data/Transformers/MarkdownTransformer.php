<?php

namespace App\Data\Transformers;

use App\Helpers\MarkdownHelper;
use Spatie\LaravelData\Support\DataProperty;
use Spatie\LaravelData\Support\Transformation\TransformationContext;
use Spatie\LaravelData\Transformers\Transformer;

class MarkdownTransformer implements Transformer
{
    public function transform(DataProperty $property, mixed $value, TransformationContext $context): mixed
    {
        if (is_string($value)) {
            return MarkdownHelper::html($value);
        }

        return $value;
    }
}
