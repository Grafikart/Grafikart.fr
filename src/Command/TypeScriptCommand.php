<?php

namespace App\Command;

use DateTime;
use DateTimeImmutable;
use Spatie\TypeScriptTransformer\Transformers\DtoTransformer;
use Spatie\TypeScriptTransformer\TypeScriptTransformer;
use Spatie\TypeScriptTransformer\TypeScriptTransformerConfig;
use Spatie\TypeScriptTransformer\Writers\ModuleWriter;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * Génère les types TypeScript à partir des DTO
 */
#[AsCommand('app:ts')]
class TypeScriptCommand
{
    public function __construct(
        #[Autowire('%kernel.project_dir%')]
        private readonly string $rootDir
    )
    {
    }

    public function __invoke(
        InputInterface  $input,
        OutputInterface $output
    ): int
    {
        $source = sprintf('%s/src/Http/Data', $this->rootDir);
        $dest = sprintf('%s/assets/types/dto.d.ts', $this->rootDir);
        $config = TypeScriptTransformerConfig::create()
            ->autoDiscoverTypes($source)
            ->defaultTypeReplacements([
                DateTime::class => 'string',
                DateTimeImmutable::class => 'string',
            ])
            ->writer(ModuleWriter::class)
            ->transformers([
                DtoTransformer::class,
            ])
            ->outputFile($dest);
        $io = new SymfonyStyle($input, $output);
        $io->note(sprintf('Reading %s', $source));
        TypeScriptTransformer::create($config)->transform();
        $io->success(sprintf('Type generated in %s', $dest));
        return Command::SUCCESS;
    }

}
