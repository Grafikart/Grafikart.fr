<?php

namespace App\Domain\Attachment\ObjectMapper;

use App\Component\ObjectMapper\TransformCallableInterface;
use App\Component\ObjectMapper\TransformCallableWithContextInterface;
use App\Infrastructure\Image\ImageResizer;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[Autoconfigure(public: true)]
readonly class ThumbnailUrlTransformer implements TransformCallableWithContextInterface
{

    public function __construct(private UploaderHelper $uploaderHelper, private ImageResizer $resizer)
    {

    }

    public function __invoke(mixed $value, object $source, ?object $target, array $context): string
    {
        return $this->resizer->resize($this->uploaderHelper->asset($source), $context['width'], $context['height']);
    }
}
