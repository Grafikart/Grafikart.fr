<?php

namespace App\Infrastructure\Importer;

use App\Domain\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class UserImporter implements TypeImporterInterface
{
    use DatabaseImporterTools;

    private \PDO $pdo;
    private EntityManagerInterface $em;

    public function __construct(\PDO $pdo, EntityManagerInterface $em)
    {
        $this->pdo = $pdo;
        $this->em = $em;
    }

    public function import(SymfonyStyle $io): void
    {
        $this->importUsers($io);
    }

    private function importUsers(SymfonyStyle $io): void
    {
        $this->truncate('`user`');
        $offset = 0;
        $io->title('Importation des utilisateurs');
        $query = $this->pdo->prepare('SELECT COUNT(id) as count FROM users');
        $query->execute();
        $result = $query->fetch();
        $io->progressStart($result['count']);
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $oldUser = [
            'id' => 0,
        ];
        while (true) {
            $query = $this->pdo->prepare("SELECT id, username, email, created_at, encrypted_password, premium, github_id, google_id, facebook_id, discord_id FROM users  ORDER BY id ASC LIMIT $offset, 1000");
            $query->execute();
            /** @var array<string,mixed> $oldUsers */
            $oldUsers = $query->fetchAll();
            if (empty($oldUsers)) {
                break;
            }
            foreach ($oldUsers as $oldUser) {
                $user = (new User())
                    ->setId($oldUser['id'])
                    ->setPremiumEnd($oldUser['premium'] && '0000-00-00 00:00:00' !== $oldUser['premium'] ? new \DateTimeImmutable($oldUser['premium']) : null)
                    ->setUsername($oldUser['username'])
                    ->setCountry($oldUser['country'] ?: 'FR')
                    ->setPassword($oldUser['encrypted_password'])
                    ->setGithubId($oldUser['github_id'])
                    ->setFacebookId($oldUser['facebook_id'])
                    ->setDiscordId($oldUser['discord_id'])
                    ->setGoogleId($oldUser['google_id'])
                    ->setCreatedAt('0000-00-00 00:00:00' === $oldUser['created_at'] ? new \DateTime('@566309532') : new \DateTime($oldUser['created_at']))
                    ->setEmail($oldUser['email']);
                $this->em->persist($user);
                $this->disableAutoIncrement($user);
                $io->progressAdvance();
            }
            $this->em->flush();
            $this->em->clear();
            $offset += 1000;
        }
        $id = $oldUser['id'] + 1;
        $this->em->getConnection()->exec("ALTER SEQUENCE user_id_seq RESTART WITH $id;");
        $this->em->getConnection()->exec('REINDEX table "user";');
        $io->progressFinish();
        $io->success(sprintf('Importation de %d utilisateurs', $result['count']));
    }

    public function support(string $type): bool
    {
        return 'users' === $type;
    }
}
