<?php

namespace App\Tool\Importer;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;
use Everyman\Neo4j\Client;
use Everyman\Neo4j\Cypher\Query;
use Everyman\Neo4j\Node;
use Everyman\Neo4j\Query\ResultSet;
use Everyman\Neo4j\Query\Row;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CoursesImporter
{

    private EntityManagerInterface $em;
    private Client $client;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->client = new Client('neo4j', 7474);
        $this->client->getTransport()->setAuth('neo4j', 'neo4j');
    }

    public function import(SymfonyStyle $io)
    {
        $this->importTechnologies($io);
        $this->importCourses($io);
    }

    private function importTechnologies(SymfonyStyle $io)
    {
        $io->title('Import des technologies');
        $this->truncate('technology');

        // On récupère les données depuis neo4j
        $result = $this->neo4jQuery(<<<CYPHER
            MATCH (t:Technology)
            RETURN t
        CYPHER);
        /** @var Row $row */
        foreach ($result as $row) {
            /** @var Node $node */
            foreach ($row as $node) {
                /** @var array{image: string, updated_at: int, name: string, category: string, content: string, slug: string} $p */
                $p = $node->getProperties();
                $t = (new Technology())
                    ->setName($p['name'])
                    ->setSlug($p['slug'])
                    ->setImage($p['image'] ?? null)
                    ->setContent($p['content'] ?? null);
                $this->em->persist($t);
                $this->disableAutoIncrement($t);
            }
        }

        // On persite
        $this->em->flush();
        $io->success(sprintf('Import de %d technologies', $result->count()));
    }

    private function importCourses(SymfonyStyle $io) {
        $io->title('Import des cours');
        $this->truncate('course');
        $this->truncate('content');

        $rows = $this->neo4jQuery(<<<CYPHER
            MATCH (t:Tutoriel)
            RETURN t
            ORDER BY t.created_at ASC
        CYPHER);
        /** @var Row $row */
        foreach ($rows as $row) {
            $tutoriel = $row->offsetGet(0)->getProperties();
            $course = (new Course())
                ->setYoutubeId($tutoriel['youtube'] ?? null)
                ->setSlug($tutoriel['slug'])
                ->setContent($tutoriel['content'] ?? null)
                ->setTitle($tutoriel['name'])
                ->setOnline(true)
                ->setDemo($tutoriel['demo'] ?? null)
                ->setDuration($tutoriel['duration'] ?? 0)
                ->setSource($tutoriel['source'] ?? false)
                ->setPremium($tutoriel['premium'] ?? false)
                ->setVideoPath($tutoriel['video'] ?? null)
                ->setCreatedAt(new \DateTime("@" . $tutoriel['created_at']))
                ->setUpdatedAt(new \DateTime("@" . $tutoriel['updated_at']))
                ->setId($tutoriel['uuid']);
            $this->disableAutoIncrement($course);
            $this->em->persist($course);
        }

        // On persite
        $this->em->flush();
        $io->success(sprintf('Import de %d cours', $rows->count()));
    }

    private function neo4jQuery(string $query): ResultSet
    {
        return (new Query($this->client, $query))->getResultSet();
    }

    private function truncate (string $tableName): void
    {
        // On vide la table
        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->exec($platform->getTruncateTableSQL($tableName, true));
    }

    private function disableAutoIncrement (object $entity) {
        $metadata = $this->em->getClassMetaData(get_class($entity));
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new AssignedGenerator());
    }
}
