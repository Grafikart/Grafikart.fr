<?php


namespace App\Infrastructure\Importer;


use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;

trait DatabaseImporterTools
{

    protected function disableAutoIncrement(object $entity): void
    {
        $metadata = $this->em->getClassMetaData(get_class($entity));
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new AssignedGenerator());
    }

    protected function truncate(string $tableName): void
    {
        // On vide la table
        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->exec($platform->getTruncateTableSQL($tableName, true));
    }

}
