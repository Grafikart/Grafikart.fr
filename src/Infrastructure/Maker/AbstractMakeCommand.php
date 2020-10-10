<?php

namespace App\Infrastructure\Maker;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Twig\Environment;

abstract class AbstractMakeCommand extends Command
{
    private Environment $twig;
    private string $projectDir;

    public function __construct(string $name = null, Environment $twig, string $projectDir)
    {
        parent::__construct($name);
        $this->twig = $twig;
        $this->projectDir = $projectDir;
    }

    protected function createFile(string $template, array $params, string $output): void
    {
        $content = $this->twig->render("@maker/$template.twig", $params);
        $filename = "{$this->projectDir}/{$output}";
        $directory = dirname($filename);
        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }
        file_put_contents($filename, $content);
    }

    /**
     * Demande à l'utilisateur de choisir une class parmis une liste correspondant au motif.
     */
    protected function askClasses(string $question, string $pattern, SymfonyStyle $io): array
    {
        // On construit la liste utilisé pour l'autocompletion
        $classes = [];
        $files = (new Finder())->in("{$this->projectDir}/src")->name('*Event.php')->files();
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $filename = str_replace('.php', '', $file->getBasename());
            $namespace = 'App\\'.str_replace('/', '\\', $file->getRelativePath()).'\\'.$filename;
            $classes[$filename] = $namespace;
        }

        // On pose à l'utilisateur la question
        $q = new Question($question);
        $q->setAutocompleterValues(array_keys($classes));
        $answers = [];
        while (true) {
            $class = $io->askQuestion($q);
            if (null === $class) {
                return $answers;
            }
            $answers[] = [
                'namespace' => $classes[$class],
                'class_name' => $class,
            ];
        }
    }
}
