<?php

namespace App\Http\Admin\Data;

use App\Http\Form\AutomaticForm;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PropertyAccess\PropertyAccessor;

/**
 * @template E of object
 *
 * @property E $entity
 */
abstract class AutomaticCrudData implements CrudDataInterface
{
    public function __construct(protected object $entity)
    {
        $reflexion = new \ReflectionClass($this);
        $properties = $reflexion->getProperties(\ReflectionProperty::IS_PUBLIC);
        $accessor = new PropertyAccessor();
        foreach ($properties as $property) {
            $name = $property->getName();
            /** @var \ReflectionNamedType|null $type */
            $type = $property->getType();
            if (
                $type
                && UploadedFile::class === $type->getName()
            ) {
                continue;
            }
            $accessor->setValue($this, $name, $accessor->getValue($entity, $name));
        }
    }

    public function getEntity(): object
    {
        return $this->entity;
    }

    public function hydrate(): void
    {
        $reflexion = new \ReflectionClass($this);
        $properties = $reflexion->getProperties(\ReflectionProperty::IS_PUBLIC);
        $accessor = new PropertyAccessor();
        foreach ($properties as $property) {
            $name = $property->getName();
            $value = $accessor->getValue($this, $name);
            $accessor->setValue($this->entity, $name, $value);
        }
    }

    public function getFormClass(): string
    {
        return AutomaticForm::class;
    }

    public function getId(): int|string|null
    {
        if (method_exists($this->entity, 'getId')) {
            return $this->entity->getId();
        }
        throw new \RuntimeException("L'entité doit avoir une méthode getId()");
    }
}
