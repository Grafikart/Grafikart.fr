<?php

namespace App\Infrastructure\Importer;

use App\Domain\Attachment\Attachment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class DataImporterHandler
{
    use DatabaseImporterTools;

    private EntityManagerInterface $em;

    private iterable $handlers = [];

    public function __construct(\Traversable $handlers, EntityManagerInterface $em)
    {
        $this->handlers = iterator_to_array($handlers);
        $this->em = $em;
    }

    /**
     * @param string|string[]|null $type
     */
    public function getImporter($type, SymfonyStyle $io): bool
    {
        $importerSupportFound = false;
        foreach ($this->handlers as $handler) {
            if ($handler->support($type)) {
                $handler->import($io);
                $importerSupportFound = true;
            }
        }

        return $importerSupportFound;
    }

    public function resetContent(): void
    {
        $this->truncate('content');
        $this->truncate($this->em->getClassMetadata(Attachment::class)->getTableName());
    }
}
