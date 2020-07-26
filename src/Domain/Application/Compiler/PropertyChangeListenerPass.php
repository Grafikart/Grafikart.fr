<?php

namespace App\Domain\Application\Compiler;

use App\Domain\Application\EventListener\DoctrinePropertyChangeEventListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Créer un système capable de suivre les changement de propriétés au sein de certaines entités.
 *
 * Tout service taggé avec doctrine.orm.property_change_listener peut observer les changement d'une propriété et voir
 * une de ces méthode appellée lors d'un changement.
 *
 *     tags:
 *       -
 *         name: 'doctrine.orm.property_change_listener'
 *         entity: 'App\Domain\Course\Entity\Course'
 *         property: 'videoaPath'
 *         method: 'updateDuration'
 *         lazy: true
 */
class PropertyChangeListenerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $resolvers = $container->findTaggedServiceIds('doctrine.orm.property_change_listener');

        // On cherche toutes les entités à observer ainsi que les attributs associés
        $entities = [];
        foreach ($resolvers as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $entities[$attributes['entity']] = $entities[$attributes['entity']] ?? [];
                $entities[$attributes['entity']][$id] = $attributes;
            }
        }

        // On crée un listener par entité à observer
        foreach ($entities as $entity => $listeners) {
            $properties = [];
            /**
             * @var string        $listenerId
             * @var array{entity: string, method: string, property: string} $listenerAttributes
             */
            foreach ($listeners as $listenerId => $listenerAttributes) {
                $properties[$listenerAttributes['property']] = $properties[$listenerAttributes['property']] ?? [];
                $properties[$listenerAttributes['property']][] = array_merge($listenerAttributes, ['listener' => new Reference($listenerId)]);
            }
            $id = 'doctrine.orm.property_change_listener'.$entity;
            $definition = new Definition(DoctrinePropertyChangeEventListener::class, [$properties]);
            $definition->setLazy(true);
            $definition->setAutowired(true);
            $definition->addTag('doctrine.orm.entity_listener', [
                'event' => 'preUpdate',
                'entity' => $entity,
                'lazy' => true,
            ]);
            $definition->addTag('doctrine.orm.entity_listener', [
                'event' => 'prePersist',
                'entity' => $entity,
                'lazy' => true,
            ]);
            $container->setDefinition($id, $definition);
        }
    }
}
