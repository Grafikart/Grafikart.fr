<?php


namespace App\Infrastructure\Importer;


use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\Mapping\ClassMetadata;

trait DatabaseImporterTools
{

    private array $idGenerator = [];

    /**
     * @param object|string $entity
     */
    protected function disableAutoIncrement($entity): void
    {
        if (!is_string($entity)) {
            $entity = get_class($entity);
        }
        /** @var ClassMetadata $metadata */
        $metadata = $this->em->getClassMetaData($entity);
        if (!isset($this->idGenerator[$entity])) {
            $this->idGenerator[$entity] = [$metadata->generatorType, $metadata->idGenerator];
        }
        $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        $metadata->setIdGenerator(new AssignedGenerator());
    }

    /**
     * @param mixed $entity
     */
    protected function restoreAutoIncrement($entity): void
    {
        if (!is_string($entity)) {
            $entity = get_class($entity);
        }
        [$type, $generator] = $this->idGenerator[$entity];
        unset($this->idGenerator[$entity]);
        $metadata = $this->em->getClassMetaData($entity);
        $metadata->setIdGeneratorType($type);
        $metadata->setIdGenerator($generator);
    }

    protected function truncate(string $tableName): void
    {
        // On vide la table
        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->exec($platform->getTruncateTableSQL($tableName, false) . ' RESTART IDENTITY CASCADE');
    }

}
