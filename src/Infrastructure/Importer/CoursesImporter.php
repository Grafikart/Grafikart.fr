<?php

namespace App\Infrastructure\Importer;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Entity\TechnologyUsage;
use Everyman\Neo4j\Node;
use Everyman\Neo4j\Query\Row;
use Everyman\Neo4j\Relationship;
use Symfony\Component\Console\Style\SymfonyStyle;

final class CoursesImporter extends Neo4jImporter
{

    public function import(SymfonyStyle $io): void
    {
        $this->importTechnologies($io);
        $this->importCourses($io);
        $this->importCourseTechnologyRelations($io);
    }

    private function importTechnologies(SymfonyStyle $io): void
    {
        $io->title('Import des technologies');
        $this->truncate('technology');

        // On récupère les données depuis neo4j
        $result = $this->neo4jQuery(<<<CYPHER
            MATCH (t:Technology)
            RETURN t
        CYPHER
        );
        /** @var Row<Node> $row */
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
            }
        }

        // On persiste
        $this->em->flush();
        $io->success(sprintf('Import de %d technologies', $result->count()));
    }

    private function importCourses(SymfonyStyle $io): void
    {
        $io->title('Import des cours');
        $this->truncate('course');
        $this->truncate('formation');
        $this->disableAutoIncrement(Content::class);

        $rows = $this->neo4jQuery(<<<CYPHER
            MATCH (t:Tutoriel)
            OPTIONAL MATCH (t)<-[:CREATE]-(u:User)
            RETURN t, u
            ORDER BY t.uuid ASC
        CYPHER
        );
        $tutoriel = [];
        /** @var Row<mixed> $row */
        foreach ($rows as $row) {
            $tutoriel = $row->offsetGet(0)->getProperties();
            $user = $row->offsetGet(1)->getProperties();
            /** @var User $author */
            $author = $this->em->getReference(User::class, $user['uuid']);
            $course = (new Course())
                ->setYoutubeId($tutoriel['youtube'] ?? null)
                ->setDemo($tutoriel['demo'] ?? null)
                ->setDuration($tutoriel['duration'] ?? 0)
                ->setSource($tutoriel['source'] ?? false)
                ->setPremium($tutoriel['premium'] ?? false)
                ->setVideoPath($tutoriel['video'] ?? null)
                ->setCreatedAt(new \DateTime("@" . $tutoriel['created_at']))
                ->setUpdatedAt(new \DateTime("@" . $tutoriel['updated_at']))
                ->setSlug($tutoriel['slug'])
                ->setContent($tutoriel['content'] ?? null)
                ->setTitle($tutoriel['name'])
                ->setOnline(true)
                ->setAuthor($author)
                ->setId($tutoriel['uuid']);
            $this->em->persist($course);
        }

        // On persiste
        $this->em->flush();
        $id = $tutoriel['uuid'] + 1;
        $this->em->getConnection()->exec("ALTER SEQUENCE content_id_seq RESTART WITH $id;");
        $this->restoreAutoIncrement(Content::class);
        $this->em->clear();
        $io->success(sprintf('Import de %d cours', $rows->count()));
    }

    private function importCourseTechnologyRelations(SymfonyStyle $io): void
    {
        $io->title('Import des relations cours <=> technologies');
        $this->truncate('technology_usage');

        $rows = $this->neo4jQuery(<<<CYPHER
            MATCH (t:Tutoriel)-[r]->(tech:Technology)
            RETURN t.uuid, r, tech.slug
        CYPHER
        );
        $technologies = array_reduce($this->em
            ->createQuery('SELECT t FROM App\Domain\Course\Entity\Technology as t')
            ->getResult(), function (array $acc, Technology $item) {
            $acc[$item->getSlug()] = $item;
            return $acc;
        }, []);
        $io->progressStart($rows->count());
        $keys = [];
        /** @var Row<mixed> $row */
        foreach ($rows as $row) {
            $courseId = $row->offsetGet(0);
            $technologySlug = $row->offsetGet(2);
            $key = $courseId . '=' . $technologySlug;
            /** @var Relationship $relation */
            $relation = $row->offsetGet(1);
            /** @var Content $content */
            $content = $this->em->getReference(Content::class, $courseId);
            $usage = (new TechnologyUsage())
                ->setVersion($relation->getProperty('version'))
                ->setSecondary($relation->getType() === 'USE')
                ->setTechnology($technologies[$technologySlug])
                ->setContent($content);
            if (!in_array($key, $keys)) {
                $this->em->persist($usage);
            }
            $keys[] = $key;
            $io->progressAdvance();
        }
        $this->em->flush();
        $io->progressFinish();
        $io->success(sprintf('Import de %d relations', $rows->count()));
    }

    public function support(string $type): bool
    {
        return $type === 'tutoriels';
    }
}
