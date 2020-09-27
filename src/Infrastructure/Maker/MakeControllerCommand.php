<?php

namespace App\Infrastructure\Maker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeControllerCommand extends AbstractMakeCommand
{
    protected static $defaultName = 'make:controller';

    protected function configure(): void
    {
        $this
            ->setDescription('Crée un controller et le test associé')
            ->addArgument('controllerName', InputArgument::OPTIONAL, 'Nom du controller')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $controllerPath = $input->getArgument('controllerName');
        if (!is_string($controllerPath)) {
            throw new \RuntimeException('controllerPath doit être une chaine de caractère');
        }
        $parts = explode('/', $controllerPath);
        if (1 === count($parts)) {
            $namespace = '';
            $className = $parts[0];
        } else {
            $namespace = '\\'.implode('\\', array_slice($parts, 0, -1));
            $className = $parts[count($parts) - 1];
        }

        $params = [
            'namespace' => $namespace,
            'class_name' => $className,
        ];

        $this->createFile('controller', $params, "src/Http/Controller/{$controllerPath}.php");
        $this->createFile('controller.test', $params, "tests/Http/Controller/{$controllerPath}Test.php");

        $io->success('Le controller a bien été créé');

        return Command::SUCCESS;
    }
}
