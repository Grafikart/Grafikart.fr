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

    public function __construct(EntityManagerInterface $em)
    {
        $this->pdo = new \PDO('mysql:host=mariadb;dbname=grafikart_dev', 'root', 'root', [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
        ]);
        $this->em = $em;
    }

    public function import(SymfonyStyle $io): void
    {
        $this->importUsers($io);
    }

    private function importUsers(SymfonyStyle $io): void
    {
        $this->truncate('`user`');
        $query = $this->pdo->prepare('SELECT id, username, email, encrypted_password FROM users  ORDER BY id ASC LIMIT 1000');
        $query->execute();
        /** @var array<string,mixed> $oldUsers */
        $oldUsers = $query->fetchAll();
        $io->title('Importation des utilisateurs');
        $io->progressStart(count($oldUsers));
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
        $io->progressFinish();
        $io->success(sprintf('Importation de %d utilisateurs', count($oldUsers)));
        $this->em->flush();
    }

}
