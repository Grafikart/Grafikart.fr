<?php

namespace App\Infrastructure\Orm;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Event\ConnectionEventArgs;
use Doctrine\DBAL\Event\SchemaColumnDefinitionEventArgs;
use Doctrine\DBAL\Event\SchemaIndexDefinitionEventArgs;
use Doctrine\DBAL\Events;
use Doctrine\ORM\Tools\Event\GenerateSchemaEventArgs;
use Doctrine\ORM\Tools\ToolEvents;

/**
 * Pour éviter que doctrine cherche à écraser ce que l'on a fait dans les migrations.
 */
class DoctrineSchemaListener implements EventSubscriber
{
    /**
     * Ignore les colonnes utilisé pour les recherches.
     */
    public function onSchemaColumnDefinition(SchemaColumnDefinitionEventArgs $eventArgs): void
    {
        if ('search_vector' === ($eventArgs->getTableColumn()['field'] ?? null)) {
            $eventArgs->preventDefault();
        }
    }

    /**
     * Ignore les index utilisés pour les recherches.
     */
    public function onSchemaIndexDefinition(SchemaIndexDefinitionEventArgs $eventArgs): void
    {
        if ('search_idx' === ($eventArgs->getTableIndex()['name'] ?? null)) {
            $eventArgs->preventDefault();
        }
    }

    /**
     * Supprime les "CREATE SCHEMA public" dans les migrations.
     */
    public function postGenerateSchema(GenerateSchemaEventArgs $eventArgs): void
    {
        $schema = $eventArgs->getSchema();

        if (!$schema->hasNamespace('public')) {
            $schema->createNamespace('public');
        }
    }

    /**
     * Force la timezone pour ne pas avoir de problème.
     */
    public function postConnect(ConnectionEventArgs $args): void
    {
        $args->getConnection()->exec("set timezone to 'Europe/Paris';");
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::onSchemaColumnDefinition,
            Events::onSchemaIndexDefinition,
            ToolEvents::postGenerateSchema,
            Events::postConnect,
        ];
    }
}
