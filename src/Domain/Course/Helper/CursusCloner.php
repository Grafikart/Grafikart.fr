<?php

namespace App\Domain\Course\Helper;

use App\Domain\Course\Entity\Cursus;

/**
 * Permet de dupliquer un cursus en prenant en compte les associations.
 */
class CursusCloner
{
    public static function clone(Cursus $cursus): Cursus
    {
        $clone = clone $cursus;
        $clone->setSlug('');
        $clone->setCreatedAt(clone $cursus->getCreatedAt());
        $usages = $clone->getTechnologyUsages();
        $clone->syncTechnologies([]);
        foreach ($usages as $usage) {
            $clone->addTechnologyUsage(clone $usage);
        }

        return $clone;
    }
}
