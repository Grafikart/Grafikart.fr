<?php

namespace App\Command;

use App\Domain\Auth\User;
use App\Domain\Notification\Entity\Notification;
use App\Domain\Password\Entity\PasswordResetToken;
use App\Domain\Profile\Entity\EmailVerification;
use App\Infrastructure\Orm\CleanableRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Vich\UploaderBundle\Handler\UploadHandler;

/**
 * Nettoie la base de données en supprimant les données non utilisées.
 */
#[AsCommand('app:clean')]
class CleanCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UploadHandler $uploaderHandler
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $this->cleanUsers($io);
        $this->clean($io, Notification::class, 'notifications');
        $this->clean($io, EmailVerification::class, 'demandes de changement d\'email');
        $this->clean($io, PasswordResetToken::class, 'demandes de reset de mdp');

        return 0;
    }

    private function cleanUsers(SymfonyStyle $io): void
    {
        $deletedUsers = $this->em->getRepository(User::class)->clean();
        foreach ($deletedUsers as $user) {
            $this->uploaderHandler->remove($user, 'avatarFile');
        }
        $io->success(sprintf('%d utilisateurs supprimés', count($deletedUsers)));
    }

    /**
     * @param class-string<object> $entityClass
     */
    private function clean(SymfonyStyle $io, string $entityClass, string $noun): void
    {
        $repository = $this->em->getRepository($entityClass);
        if (!($repository instanceof CleanableRepositoryInterface)) {
            throw new \Exception(sprintf("%s n'implémente pas la CleanableRepositoryInterface", $repository::class));
        }
        $count = $repository->clean();
        $io->success(sprintf('%d %s supprimés', $count, $noun));
    }
}
