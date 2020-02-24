<?php

namespace App\Twig;

use App\Domain\Attachment\Attachment;
use App\Domain\Attachment\AttachmentUrlGenerator;
use App\Infrastructure\Image\ImageResizer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TwigPathExtension extends AbstractExtension
{

    private UploaderHelper $uploaderHelper;
    private ImageResizer $imageResizer;
    private AttachmentUrlGenerator $attachmentUrlGenerator;

    public function __construct(
        ImageResizer $imageResizer,
        AttachmentUrlGenerator $attachmentUrlGenerator
    )
    {
        $this->imageResizer = $imageResizer;
        $this->attachmentUrlGenerator = $attachmentUrlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploads_path', [$this, 'uploadsPath']),
            new TwigFunction('image_url', [$this, 'imageUrl'])
        ];
    }

    public function uploadsPath(string $path): string
    {
        return '/uploads/' . trim($path, '/');
    }

    public function imageUrl(Attachment $attachment, int $width, int $height): string
    {
        return $this->imageResizer->resize($this->attachmentUrlGenerator->generate($attachment), $width, $height);
    }
}
