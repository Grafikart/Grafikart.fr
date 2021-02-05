<?php

namespace App\Infrastructure\Maker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeAdminCommand extends AbstractMakeCommand
{
    protected static $defaultName = 'do:admin';

    protected function configure(): void
    {
        $this
            ->setDescription('Crée un crud pour gérer un contenu')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $entity = $this->askClass('Pour quelle entité', 'Domain/*/Entity/*', $io);
        $route = $io->askQuestion(new Question('Quelle route ?'));
        $slug = $io->askQuestion(new Question('Quel slug ?'));
        $entityName = $entity['class_name'];
        $entity = $entity['namespace'];
        $params = [
            'route' => $route,
            'slug' => $slug,
            'entity' => $entity,
            'entity_name' => $entityName,
        ];

        $this->createFile('admin/controller', $params, "src/Http/Admin/Controller/{$entityName}Controller.php");
        $this->createFile('admin/CrudData.php', $params, "src/Http/Admin/Data/{$entityName}CrudData.php");

        $paths = ['_form', 'edit', 'new', 'index'];
        foreach ($paths as $path) {
            $this->createFile("admin/$path.html", $params, "templates/admin/$slug/$path.html.twig");
        }

        $io->success("L'administration a bien été créé");

        return Command::SUCCESS;
    }
}
