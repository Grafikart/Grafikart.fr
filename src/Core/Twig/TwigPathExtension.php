<?php

namespace App\Core\Twig;

use App\Domain\Attachment\AttachmentUrlGenerator;
use App\Domain\Attachment\Validator\NonExistingAttachment;
use App\Infrastructure\Image\ImageResizer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TwigPathExtension extends AbstractExtension
{

    private UploaderHelper $uploaderHelper;
    private ImageResizer $imageResizer;
    private UploaderHelper $helper;

    public function __construct(
        ImageResizer $imageResizer,
        UploaderHelper $helper
    ) {
        $this->imageResizer = $imageResizer;
        $this->helper = $helper;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('uploads_path', [$this, 'uploadsPath']),
            new TwigFunction('image_url', [$this, 'imageUrl']),
            new TwigFunction('image', [$this, 'imageTag'], ['is_safe' => ['html']])
        ];
    }

    public function uploadsPath(string $path): string
    {
        return '/uploads/' . trim($path, '/');
    }

    public function imageUrl(?object $entity, ?int $width = null, ?int $height = null): ?string
    {
        if ($entity === null || $entity instanceof NonExistingAttachment) {
            return null;
        }
        return $this->imageResizer->resize($this->helper->asset($entity), $width, $height);
    }

    public function imageTag(?object $entity, ?int $width = null, ?int $height = null): ?string
    {
        $url = $this->imageUrl($entity, $width, $height);
        if ($url !== null) {
            return "<img src=\"{$url}\" width=\"{$width}\" height=\"{$height}\"/>";
        }
        return null;
    }
}
