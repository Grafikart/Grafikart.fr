<?php

namespace App\Domain\Attachment\Normalizer;

use App\Domain\Attachment\Attachment;
use App\Domain\Attachment\AttachmentUrlGenerator;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AttachmentApiNormalizer implements NormalizerInterface
{

    private AttachmentUrlGenerator $urlGenerator;

    public function __construct(AttachmentUrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
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
            'url' => $this->urlGenerator->generate($object)
        ];
    }

    /**
     * @param mixed $data;
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Attachment && $format === 'json';
    }
}
