<?php

namespace App\Infrastructure\Maker;

use Symfony\Component\Console\Command\Command;
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
}
