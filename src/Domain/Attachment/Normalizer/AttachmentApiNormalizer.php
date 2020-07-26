<?php

namespace App\Domain\Attachment\Normalizer;

use App\Domain\Attachment\Attachment;
use App\Infrastructure\Image\ImageResizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class AttachmentApiNormalizer implements NormalizerInterface
{
    private ImageResizer $resizer;
    private UploaderHelper $uploaderHelper;

    public function __construct(
        UploaderHelper $uploaderHelper,
        ImageResizer $resizer
    ) {
        $this->resizer = $resizer;
        $this->uploaderHelper = $uploaderHelper;
    }

    /**
     * @param Attachment $object
     */
    public function normalize($object, string $format = null, array $context = []): array
    {
        $info = pathinfo($object->getFileName());
        $filenameParts = explode('-', $info['filename']);
        $filenameParts = array_slice($filenameParts, 0, -1);
        $filename = implode('-', $filenameParts);
        $extension = $info['extension'] ?? '';

        return [
            'id' => $object->getId(),
            'createdAt' => $object->getCreatedAt()->getTimestamp(),
            'name' => "{$filename}.{$extension}",
            'size' => $object->getFileSize(),
            'url' => $this->resizer->resize($this->uploaderHelper->asset($object), 250, 100),
        ];
    }

    /**
     * @param mixed $data ;
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Attachment && 'json' === $format;
    }
}
