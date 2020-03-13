<?php

namespace App\Infrastructure\Importer;

use App\Domain\Attachment\Attachment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class DataImporter implements TypeImporterInterface
{
    protected KernelInterface $kernel;
    protected EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em, KernelInterface $kernel)
    {
        $this->em = $em;
        $this->em->getConnection()->getConfiguration()->setSQLLogger(null);
        $this->kernel = $kernel;
    }

    /**
     * Convertit le chemin d'un "ancien" fichier en Attachment
     */
    protected function oldFileToAttachment(string $filename, \DateTime $createdAt): ?Attachment
    {
        $filePath = "{$this->kernel->getProjectDir()}/public/old/{$filename}";
        if (file_exists($filePath)) {
            $file = new ImportedFile($filePath);
            return (new Attachment())
                ->setFile($file)
                ->setCreatedAt($createdAt);
        }
        return null;
    }

}
