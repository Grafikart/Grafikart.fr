<?php

namespace App\Http\Cms\Data\Attachment;

use App\Component\ObjectMapper\Attribute\Map;
use App\Component\ObjectMapper\Transform\TimestampTransformer;
use App\Domain\Attachment\ObjectMapper\AttachmentUrlTransformer;
use App\Domain\Attachment\ObjectMapper\ThumbnailUrlTransformer;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

/**
 * Représente un fichier pour l'explorateur de fichier
 */
#[TypeScript]
readonly class AttachmentFileData
{
    public function __construct(
        public int $id,
        #[Map(source: 'createdAt', transform: TimestampTransformer::class)]
        public int $createdAt,
        #[Map(source: 'fileName', transform: [self::class, 'formatName'])]
        public string $name,
        #[Map(source: 'fileSize')]
        public int $size,
        #[Map(source: 'file', transform: AttachmentUrlTransformer::class)]
        public mixed $url,
        #[Map(source: 'file', transform: ThumbnailUrlTransformer::class, context: ['width' => 250, 'height' => 100])]
        public string $thumbnail,
    ) {}

    public static function formatName(string $filename)
    {
        $info = pathinfo($filename);
        $filenameParts = explode('-', $info['filename']);
        $filenameParts = array_slice($filenameParts, 0, -1);
        $filename = implode('-', $filenameParts);
        $extension = $info['extension'] ?? '';

        return sprintf('%s.%s', $filename, $extension);
    }
}
