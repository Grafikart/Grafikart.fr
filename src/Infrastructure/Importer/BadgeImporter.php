<?php

namespace App\Infrastructure\Importer;

use App\Domain\Auth\User;
use App\Domain\Badge\Entity\Badge;
use App\Domain\Badge\Entity\BadgeUnlock;
use Symfony\Component\Console\Style\SymfonyStyle;

final class BadgeImporter extends MySQLImporter
{
    public function import(SymfonyStyle $io): void
    {
        $this->importBadges($io);
        $this->importUnlocks($io);
    }

    public function importBadges(SymfonyStyle $io): void
    {
        $this->truncate('badge');
        $query = $this->pdo->prepare(<<<SQL
            SELECT b.id, b.name, b.content, b.position, b.image, b.action, b.action_count, b.theme
            FROM badges as b
        SQL);
        $query->execute();
        /** @var array<mixed> $rows */
        $rows = $query->fetchAll();
        $io->title('Importation des badges');
        $io->progressStart(count($rows));
        $row = ['id' => 0];
        foreach ($rows as $row) {
            $badge = (new Badge(
                $row['name'],
                $row['content'],
                $row['action'],
                $row['action_count'],
                $row['theme'],
            ))->setId($row['id']);
            $this->disableAutoIncrement($badge);

            $filePath = "{$this->kernel->getProjectDir()}/public/old/badges/{$row['image']}";
            if (file_exists($filePath)) {
                $badge->setImageFile(new ImportedFile($filePath));
            }

            $this->em->persist($badge);
            $io->progressAdvance();
        }
        $id = $row['id'] + 1;
        $this->em->getConnection()->exec("ALTER SEQUENCE badge_id_seq RESTART WITH $id;");
        $this->em->getConnection()->exec('REINDEX table "badge";');
        $io->progressFinish();
        $io->success(sprintf('Importation de %d badges', count($rows)));
        $this->em->flush();
    }

    public function importUnlocks(SymfonyStyle $io): void
    {
        $this->truncate('badge_unlock');
        $offset = 0;
        $io->title('Importation des déblocages');
        // On compte le nbre de déblocage à migrer
        $query = $this->pdo->prepare('SELECT COUNT(id) as count FROM badges_users');
        $query->execute();
        $result = $query->fetch();
        $io->progressStart($result['count']);
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        while (true) {
            $query = $this->pdo->prepare(<<<SQL
            SELECT user_id, badge_id, created_at
            FROM badges_users as bu
            ORDER BY id ASC LIMIT $offset, 1000
        SQL);
            $query->execute();
            /** @var array<mixed> $rows */
            $rows = $query->fetchAll();
            if (empty($rows)) {
                break;
            }
            foreach ($rows as $row) {
                $user = $this->em->getRepository(User::class)->find($row['user_id']);
                $badge = $this->em->getReference(Badge::class, $row['badge_id']);
                if ($user && $badge) {
                    $badge = (new BadgeUnlock(
                        $user,
                        $badge,
                    ))->setCreatedAt(new \DateTime($row['created_at']));
                    $this->em->persist($badge);
                }
                $io->progressAdvance();
            }
            $this->em->flush();
            $this->em->clear();
            $offset += 1000;
        }
        $io->progressFinish();
        $io->success(sprintf('Importation de %d déblocages de badges', count($rows)));
        $this->em->flush();
        $this->em->clear();
    }

    public function support(string $type): bool
    {
        return 'badges' === $type;
    }
}
