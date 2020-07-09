<?php

namespace App\Domain\Application\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Ecouteur.
 */
class DoctrinePropertyChangeEventListener
{
    private array $listeners;
    private PropertyAccessorInterface $propertyAccessor;

    /**
     * @param array<string, mixed> $listeners
     */
    public function __construct(array $listeners, PropertyAccessorInterface $propertyAccessor)
    {
        $this->listeners = $listeners;
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param object $entity
     *
     * @return void
     */
    public function prePersist($entity, LifecycleEventArgs $event)
    {
        foreach ($this->listeners as $key => $listeners) {
            if ($value = $this->propertyAccessor->getValue($entity, $key)) {
                foreach ($listeners as $listener) {
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
        $changeSet = $event->getEntityManager()->getUnitOfWork()->getEntityChangeSet($entity);
        foreach ($this->listeners as $key => $listeners) {
            if (in_array($key, array_keys($changeSet))) {
                foreach ($listeners as $listener) {
                    $method = $listener['method'];
                    $listener['listener']->$method($entity, $changeSet[$key][1], $changeSet[$key][0]);
                }
            }
        }
    }
}
