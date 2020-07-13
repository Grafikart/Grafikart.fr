<?php

namespace App\Infrastructure\Importer;

use App\Domain\Auth\User;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Formation;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Entity\TechnologyUsage;
use Everyman\Neo4j\Query\Row;
use Everyman\Neo4j\Relationship;
use Symfony\Component\Console\Style\SymfonyStyle;

final class FormationImporter extends Neo4jImporter
{
    public function import(SymfonyStyle $io): void
    {
        $this->importFormations($io);
        $this->importChapters($io);
        $this->importFormationsTechnologyRelations($io);
    }

    private function importFormations(SymfonyStyle $io): void
    {
        $io->title('Import des formations');
        /** @var User $author */
        $author = $this->em->getReference(User::class, 1);

        $rows = $this->neo4jQuery(<<<CYPHER
            MATCH (f:Formation)
            RETURN f
            ORDER BY f.created_at ASC
        CYPHER
        );
        $io->progressStart($rows->count());
        /** @var Row<mixed> $row */
        foreach ($rows as $row) {
            $data = $row->offsetGet(0)->getProperties();
            $item = (new Formation())
                ->setYoutubePlaylist($data['youtube_playlist'] ?? null)
                ->setShort($data['short'] ?? null)
                // TODO : gérer l'import d'image via les attachments
                // ->setImage($data['image'] ?? null)
                ->setCreatedAt(new \DateTime('@'.$data['created_at']))
                ->setUpdatedAt(new \DateTime('@'.$data['updated_at']))
                ->setTitle($data['name'])
                ->setSlug($data['slug'])
                ->setContent($data['content'] ?? null)
                ->setAuthor($author)
                ->setOnline(true);
            $this->em->persist($item);
            $io->progressAdvance();
        }

        // On persiste
        $this->em->flush();
        $io->progressFinish();
        $io->success(sprintf('Import de %d formations', $rows->count()));
    }

    private function importChapters(SymfonyStyle $io): void
    {
        $io->title('Import des chapitres');

        $rows = $this->neo4jQuery(<<<CYPHER
            MATCH (f:Formation)
            RETURN f.slug as slug
        CYPHER
        );

        $io->progressStart($rows->count());
        foreach ($rows as $row) {
            $this->importChapterFor($row->offsetGet(0));
            $io->progressAdvance();
        }

        // On persiste
        $this->em->flush();
        $io->progressFinish();
        $io->success(sprintf('Import des chapitres des %d formations', $rows->count()));
    }

    private function importChapterFor(string $formationSlug): void
    {
        /** @var Formation|null $formation */
        $formation = $this->em->getRepository(Formation::class)->findOneBy(['slug' => $formationSlug]);
        if (null === $formation) {
            throw new \Exception("Impossible de trouver la formation \"$formationSlug\"");
        }
        $rows = $this->neo4jQuery(
            <<<CYPHER
            MATCH (f:Formation)-[:INCLUDE]->(c:Chapter)-[:INCLUDE]->(t:Tutoriel)
            WHERE f.slug = "$formationSlug"
            WITH c.name as chapter, t.uuid as tutoriel, size((t)-[:REQUIRE*]->(:Tutoriel)<-[:INCLUDE*]-(f)) as children
            ORDER BY children ASC
            RETURN chapter, collect(tutoriel)
        CYPHER
        );
        $chapters = [];
        /** @var Row<mixed> $row */
        foreach ($rows as $row) {
            /** @var int[] $courses */
            $courses = iterator_to_array($row->offsetGet(1));
            $chapters[] = [
                'title' => $row->offsetGet(0),
                'courses' => $courses,
            ];
            foreach ($courses as $id) {
                /** @var Course $course */
                $course = $this->em->getReference(Course::class, $id);
                $formation->addCourse($course);
            }
        }
        $formation->setRawChapters($chapters);
    }

    private function importFormationsTechnologyRelations(SymfonyStyle $io): void
    {
        $io->title('Import des relations formations <=> technologies');
        $rows = $this->neo4jQuery(<<<CYPHER
            MATCH (f:Formation)-[r]->(tech:Technology)
            RETURN f.slug, r, tech.slug
        CYPHER
        );
        $technologies = array_reduce($this->em
            ->createQuery('SELECT t FROM App\Domain\Course\Entity\Technology as t')
            ->getResult(), function (array $acc, Technology $item) {
                $acc[$item->getSlug()] = $item;

                return $acc;
            }, []);
        $formations = array_reduce($this->em->getRepository(Formation::class)->findAll(), function (array $acc, Formation $f) {
            $acc[$f->getSlug()] = $f;

            return $acc;
        }, []);
        $io->progressStart($rows->count());
        $keys = [];
        /** @var Row<mixed> $row */
        foreach ($rows as $row) {
            $formationSlug = $row->offsetGet(0);
            $technologySlug = $row->offsetGet(2);
            $key = $formationSlug.'='.$technologySlug;
            /** @var Relationship $relation */
            $relation = $row->offsetGet(1);
            $usage = (new TechnologyUsage())
                ->setVersion($relation->getProperty('version'))
                ->setSecondary('USE' === $relation->getType())
                ->setTechnology($technologies[$technologySlug])
                ->setContent($formations[$formationSlug]);
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
        return 'formations' === $type;
    }
}
