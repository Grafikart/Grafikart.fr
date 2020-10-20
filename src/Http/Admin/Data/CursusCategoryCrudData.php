<?php

declare(strict_types=1);

namespace App\Http\Admin\Data;

use App\Domain\Course\Entity\CursusCategory;

/**
 * @property CursusCategory $entity
 */
class CursusCategoryCrudData extends AutomaticCrudData
{
    public string $name;

    public int $position;

    public string $description;
}
