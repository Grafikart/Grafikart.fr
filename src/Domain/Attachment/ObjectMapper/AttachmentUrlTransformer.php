<?php

namespace App\Domain\Attachment\ObjectMapper;

use App\Component\ObjectMapper\TransformCallableInterface;
use App\Domain\Attachment\Attachment;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

#[Autoconfigure(public: true)]
final class AttachmentUrlTransformer implements TransformCallableInterface
{

    public function __construct(private UploaderHelper $uploaderHelper){

    }

    public function __invoke(mixed $value, object $source, ?object $target): ?string
    {
        if ($value instanceof Attachment) {
            return $this->uploaderHelper->asset($value);
        }
        if ($source instanceof Attachment) {
            return $this->uploaderHelper->asset($source);
        }
        return null;
    }
}
