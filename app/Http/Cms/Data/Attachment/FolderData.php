<?php

namespace App\Http\Cms\Data\Attachment;

use Spatie\TypeScriptTransformer\Attributes\TypeScript;

/**
 * Represente un dossier dans le système d'explorateur de fichier
 */
#[TypeScript]
readonly class FolderData
{
    public function __construct(
        public string $path,
        public int $count,
    ) {}

}
