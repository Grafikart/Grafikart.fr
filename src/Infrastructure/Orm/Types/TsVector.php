<?php

namespace App\Infrastructure\Orm\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Class TsVector.
 */
class TsVector extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'tsvector';
    }

    public function canRequireSQLConversion()
    {
        return false;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValueSQL($sqlExp, AbstractPlatform $platform)
    {
        return sprintf("to_tsvector('french', %s)", $sqlExp);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value['data'];
    }

    /**
     * Gets the name of this type.
     *
     * @return string
     */
    public function getName()
    {
        return 'tsvector';
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform)
    {
        return ['tsvector'];
    }
}
