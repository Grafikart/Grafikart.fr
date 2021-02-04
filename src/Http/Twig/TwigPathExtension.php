<?php

namespace App\Http\Twig;

use App\Domain\Attachment\Validator\NonExistingAttachment;
use App\Infrastructure\Image\ImageResizer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class TwigPathExtension extends AbstractExtension
{
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
            new TwigFunction('image_url_raw', [$this, 'imageUrlRaw']),
            new TwigFunction('image', [$this, 'imageTag'], ['is_safe' => ['html']]),
        ];
    }

    public function uploadsPath(string $path): string
    {
        return '/uploads/'.trim($path, '/');
    }

    public function imageUrl(?object $entity, ?int $width = null, ?int $height = null): ?string
    {
        if (null === $entity || $entity instanceof NonExistingAttachment) {
            return null;
        }

        $path = $this->helper->asset($entity);

        if (null === $path) {
            return null;
        }

        if ('jpg' !== pathinfo($path, PATHINFO_EXTENSION)) {
            return $path;
        }

        return $this->imageResizer->resize($this->helper->asset($entity), $width, $height);
    }

    public function imageUrlRaw(?object $entity): string
    {
        if (null === $entity || $entity instanceof NonExistingAttachment) {
            return '';
        }

        return $this->helper->asset($entity) ?: '';
    }

    public function imageTag(?object $entity, ?int $width = null, ?int $height = null): ?string
    {
        $url = $this->imageUrl($entity, $width, $height);
        if (null !== $url) {
            return "<img src=\"{$url}\" width=\"{$width}\" height=\"{$height}\"/>";
        }

        return null;
    }
}
