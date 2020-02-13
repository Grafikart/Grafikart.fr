<?php

namespace App\Infrastructure\Importer;

use Doctrine\ORM\EntityManagerInterface;
use Everyman\Neo4j\Client;
use Everyman\Neo4j\Cypher\Query;
use Everyman\Neo4j\Query\ResultSet;
use Everyman\Neo4j\Query\Row;

abstract class Neo4jImporter
{

    use DatabaseImporterTools;

    protected EntityManagerInterface $em;
    protected Client $client;

    public function __construct(EntityManagerInterface $em, Client $client)
    {
        $this->em = $em;
        $this->client = $client;
    }

    /**
     * @return ResultSet<Row>
     */
    protected function neo4jQuery(string $query): ResultSet
    {
        return (new Query($this->client, $query))->getResultSet();
    }
}
