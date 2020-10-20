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
    protected string $projectDir;

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
    protected function askClass(string $question, string $pattern, SymfonyStyle $io, bool $multiple = false): array
    {
        // On construit la liste utilisé pour l'autocompletion
        $classes = [];
        $paths = explode('/', $pattern);
        if (1 === count($paths)) {
            $directory = "{$this->projectDir}/src";
            $pattern = $pattern;
        } else {
            $directory = "{$this->projectDir}/src/".join('/', array_slice($paths, 0, -1));
            $pattern = join('/', array_slice($paths, -1));
        }
        $files = (new Finder())->in($directory)->name($pattern.'.php')->files();
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $filename = str_replace('.php', '', $file->getBasename());
            $classes[$filename] = $file->getPathname();
        }

        // On pose à l'utilisateur la question
        $q = new Question($question);
        $q->setAutocompleterValues(array_keys($classes));
        $answers = [];
        $replacements = [
            "{$this->projectDir}/src" => 'App',
            '/' => '\\',
            '.php' => '',
        ];

        while (true) {
            $class = $io->askQuestion($q);
            if (null === $class) {
                return $answers;
            }
            $path = $classes[$class];

            $answers[] = [
                'namespace' => str_replace(array_keys($replacements), array_values($replacements), $path),
                'class_name' => $class,
            ];
            if (false === $multiple) {
                return $answers[0];
            }
        }
    }

    /**
     * Demande à l'utilisateur de choisir un domaine.
     */
    protected function askDomain(SymfonyStyle $io): string
    {
        // On construit la liste utilisé pour l'autocompletion
        $domains = [];
        $files = (new Finder())->in("{$this->projectDir}/src/Domain")->depth(0)->directories();
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $domains[] = $file->getBasename();
        }

        // On pose à l'utilisateur la question
        $q = new Question('Sélectionner un domaine');
        $q->setAutocompleterValues($domains);

        return $io->askQuestion($q);
    }
}
