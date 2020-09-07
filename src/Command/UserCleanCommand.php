<?php

namespace App\Command;

use App\Domain\Auth\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class UserCleanCommand extends Command
{
    protected static $defaultName = 'app:user:clean';

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $deletions = $this->userRepository->cleanUsers();
        $io->success(sprintf('%d utilisateurs supprimés', $deletions));

        return 0;
    }
}
