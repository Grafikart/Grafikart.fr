<?php

namespace App\Infrastructure\Importer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Everyman\Neo4j\Client;
use Everyman\Neo4j\Cypher\Query;
use Everyman\Neo4j\Query\ResultSet;
use Everyman\Neo4j\Query\Row;

abstract class Neo4jImporter
{

    protected EntityManagerInterface $em;
    protected Client $client;

    public function __construct(EntityManagerInterface $em, Client $client)
    {
        $this->em = $em;
        $this->client = $client;
    }

    protected function disableAutoIncrement(object $entity): void
    {
        $metadata = $this->em->getClassMetaData(get_class($entity));
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new AssignedGenerator());
    }

    protected function truncate(string $tableName): void
    {
        // On vide la table
        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->exec($platform->getTruncateTableSQL($tableName, true));
    }

    /**
     * @return ResultSet<Row>
     */
    protected function neo4jQuery(string $query): ResultSet
    {
        return (new Query($this->client, $query))->getResultSet();
    }
}
