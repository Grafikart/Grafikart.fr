<?php

namespace App\Infrastructure\Importer;

use App\Domain\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class UserImporter
{

    use DatabaseImporterTools;

    private \PDO $pdo;
    private EntityManagerInterface $em;

    public function __construct(\PDO $pdo, EntityManagerInterface $em)
    {
        $this->pdo = $pdo;
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);
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
        $query = $this->pdo->prepare("SELECT COUNT(id) as count FROM users");
        $query->execute();
        $result = $query->fetch();
        $io->progressStart($result['count']);
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        while (true) {
            $query = $this->pdo->prepare("SELECT id, username, email, encrypted_password FROM users  ORDER BY id ASC LIMIT $offset, 1000");
            $query->execute();
            /** @var array<string,mixed> $oldUsers */
            $oldUsers = $query->fetchAll();
            if (empty($oldUsers)) {
                break;
            }
            foreach($oldUsers as $oldUser) {
                $user = (new User())
                    ->setId($oldUser['id'])
                    ->setUsername($oldUser['username'])
                    ->setPassword($oldUser['encrypted_password'])
                    ->setEmail($oldUser['email']);
                $this->em->persist($user);
                $this->disableAutoIncrement($user);
                $io->progressAdvance();
            }
            $this->em->flush();
            $this->em->clear();
            $offset += 1000;
        }
        $io->progressFinish();
        $io->success(sprintf('Importation de %d utilisateurs', $result['count']));
    }

}
