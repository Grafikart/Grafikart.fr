<?php

namespace App\Infrastructure\Importer;

use Doctrine\ORM\EntityManagerInterface;
use Everyman\Neo4j\Client;
use Everyman\Neo4j\Cypher\Query;
use Everyman\Neo4j\Query\ResultSet;
use Everyman\Neo4j\Query\Row;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class Neo4jImporter extends DataImporter
{
    use DatabaseImporterTools;

    protected Client $client;

    public function __construct(EntityManagerInterface $em, Client $client, KernelInterface $kernel)
    {
        $this->client = $client;
        parent::__construct($em, $kernel);
    }

    /**
     * @return ResultSet<Row>
     */
    protected function neo4jQuery(string $query): ResultSet
    {
        return (new Query($this->client, $query))->getResultSet();
    }
}
