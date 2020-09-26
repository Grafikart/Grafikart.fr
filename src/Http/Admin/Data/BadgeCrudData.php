<?php

namespace App\Http\Admin\Data;

use App\Core\Validator\Unique;
use App\Domain\Badge\Entity\Badge;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Unique(field="name")
 *
 * @property Badge $entity
 */
class BadgeCrudData extends AutomaticCrudData
{
    /**
     * @Assert\NotBlank()
     */
    public string $name = '';

    /**
     * @Assert\NotBlank()
     */
    public string $description = '';

    /**
     * @Assert\NotBlank()
     */
    public string $action = '';

    /**
     * @Assert\NotBlank()
     */
    public int $actionCount = 0;

    public function hydrate(): void
    {
        parent::hydrate();
    }
}
