<?php


namespace App\Domain\Application\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Ecouteur
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

    public function prePersist(object $entity, LifecycleEventArgs $event): void
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

    public function preUpdate(object $entity, PreUpdateEventArgs $event): void
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
