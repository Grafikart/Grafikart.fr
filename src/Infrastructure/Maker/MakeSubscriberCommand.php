<?php

namespace App\Infrastructure\Maker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MakeSubscriberCommand extends AbstractMakeCommand
{
    protected static $defaultName = 'do:subscriber';

    protected function configure(): void
    {
        $this
            ->setDescription('Crée un EventSubscriber et le test associé')
            ->addArgument('subscriberName', InputArgument::OPTIONAL, "Nom de l'EventSubscriber")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $events = $this->askClass('Quel évènement écouter', '*Event', $io, true);

        /** @var string $subscriberPath */
        $subscriberPath = $input->getArgument('subscriberName');
        if ('Subscriber' !== substr($subscriberPath, -1 * strlen('Subscriber'))) {
            $subscriberPath .= 'Subscriber';
        }
        if (!is_string($subscriberPath)) {
            throw new \RuntimeException('subscriberPath doit être une chaine de caractère');
        }
        $parts = explode('/', $subscriberPath);
        $namespace = '\\'.implode('\\', array_slice($parts, 0, -1));
        $className = $parts[count($parts) - 1];
        $basePath = 'src/';
        $params = [
            'events' => $events,
            'namespace' => $namespace,
            'class_name' => $className,
        ];

        $this->createFile('eventSubscriber', $params, "{$basePath}{$subscriberPath}.php");
        $this->createFile('eventSubscriber.test', $params, str_replace('src/', 'tests/', $basePath)."{$subscriberPath}Test.php");

        $io->success('Le subscriber a bien été créé');

        return Command::SUCCESS;
    }
}
