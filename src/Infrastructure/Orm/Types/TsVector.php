<?php

namespace App\Infrastructure\Orm\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Class TsVector.
 */
class TsVector extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        return 'tsvector';
    }

    public function canRequireSQLConversion(): bool
    {
        return false;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        return $value;
    }

    public function convertToDatabaseValueSQL($sqlExp, AbstractPlatform $platform): string
    {
        return sprintf("to_tsvector('french', %s)", $sqlExp);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value['data'];
    }

    /**
     * Gets the name of this type.
     */
    public function getName(): string
    {
        return 'tsvector';
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return ['tsvector'];
    }
}
