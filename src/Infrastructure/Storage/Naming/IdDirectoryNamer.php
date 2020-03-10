<?php

namespace App\Infrastructure\Storage\Naming;

use App\Domain\Application\Entity\Content;
use Vich\UploaderBundle\Mapping\PropertyMapping;
use Vich\UploaderBundle\Naming\DirectoryNamerInterface;

class IdDirectoryNamer implements DirectoryNamerInterface
{

    /**
     * @param Content $object
     */
    public function directoryName($object, PropertyMapping $mapping): string
    {
        return strval(ceil($object->getId() / 1000));
    }
}
