<?php

namespace App\Infrastructure\TypeScript;

use Spatie\LaravelTypeScriptTransformer\LaravelData\Transformers\DataClassTransformer;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;
use Spatie\TypeScriptTransformer\PhpNodes\PhpClassNode;

class AttributedDataClassTransformer extends DataClassTransformer
{
    protected function shouldTransform(PhpClassNode $phpClassNode): bool
    {
        return parent::shouldTransform($phpClassNode)
            && count($phpClassNode->getAttributes(TypeScript::class)) > 0;
    }
}
