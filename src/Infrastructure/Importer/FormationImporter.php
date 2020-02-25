<?php

namespace App\Infrastructure\Importer;

use App\Domain\Course\Entity\Formation;
use Everyman\Neo4j\Query\Row;
use Symfony\Component\Console\Style\SymfonyStyle;

final class FormationImporter extends Neo4jImporter
{

    public function import(SymfonyStyle $io): void
    {
        $this->importFormations($io);
        $this->importChapters($io);
    }

    private function importFormations(SymfonyStyle $io): void
    {
        $io->title('Import des formations');
        $this->truncate('formation');

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
                ->setImage($data['image'] ?? null)
                ->setCreatedAt(new \DateTime("@" . $data['created_at']))
                ->setUpdatedAt(new \DateTime("@" . $data['updated_at']))
                ->setTitle($data['name'])
                ->setSlug($data['slug'])
                ->setContent($data['content'] ?? null)
                ->setOnline(true)
            ;
            $this->em->persist($item);
            $io->progressAdvance();
        }

        // On persite
        $this->em->flush();
        $io->progressFinish();
        $io->success(sprintf('Import de %d formations', $rows->count()));
    }

    private function importChapters(SymfonyStyle $io): void
    {
        $io->title('Import des chapitres');

        $rows = $this->neo4jQuery(
        <<<CYPHER
            MATCH (f:Formation)
            OPTIONAL MATCH (f)-[:INCLUDE]->(c:Chapter)
            OPTIONAL MATCH (c)-[:INCLUDE]->(t:Tutoriel)
            WITH f.slug as formation, c.name as chapter, t.uuid as tutoriel, size((t)-[:REQUIRE*]->(:Tutoriel)<-[:INCLUDE*]-(f)) as children
            ORDER BY children ASC
            RETURN formation, chapter, collect(tutoriel)
        CYPHER);
        $io->progressStart($rows->count());
        /** @var Row<mixed> $row */
        foreach ($rows as $row) {
            $formationSlug = $row->offsetGet(0);
            $chapterTitle = $row->offsetGet(1);
            $coursesIds = iterator_to_array($row->offsetGet(2));
            // TODO : Gérer la création des chapitres dans les formations
            break;
        }
        $io->progressFinish();

    }

}
