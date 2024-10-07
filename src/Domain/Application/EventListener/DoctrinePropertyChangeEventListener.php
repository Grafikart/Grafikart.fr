<?php

namespace App\Domain\Application\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Ecouteur.
 */
class DoctrinePropertyChangeEventListener
{
    /**
     * @param array<string, mixed> $listeners
     */
    public function __construct(private readonly array $listeners, private readonly PropertyAccessorInterface $propertyAccessor)
    {
    }

    /**
     * @param object $entity
     *
     * @return void
     */
    public function prePersist($entity)
    {
        $listeners = $this->listeners[$entity::class] ?? null;
        if (null === $listeners) {
            return;
        }
        foreach ($listeners as $key => $propertyListeners) {
            if ($value = $this->propertyAccessor->getValue($entity, $key)) {
                foreach ($propertyListeners as $listener) {
                    $method = $listener['method'];
                    $listener['listener']->$method($entity, $value, null);
                }
            }
        }
    }

    /**
     * @param object $entity
     *
     * @return void
     */
    public function preUpdate($entity, PreUpdateEventArgs $event)
    {
        $listeners = $this->listeners[$entity::class] ?? null;
        if (null === $listeners) {
            return;
        }
        $changeSet = $event->getObjectManager()->getUnitOfWork()->getEntityChangeSet($entity);
        foreach ($listeners as $key => $propertyListeners) {
            if (in_array($key, array_keys($changeSet))) {
                foreach ($propertyListeners as $listener) {
                    $method = $listener['method'];
                    $listener['listener']->$method($entity, $changeSet[$key][1], $changeSet[$key][0]);
                }
            }
        }
    }
}
