<?php

namespace App\Http\Admin\Data;

use App\Core\Validator\Slug;
use App\Core\Validator\Unique;
use App\Domain\Course\Entity\Technology;
use App\Domain\Forum\Entity\Tag;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Unique(field="slug")
 * @Unique(field="name")
 * @property Technology $entity
 */
class ForumTagCrudData extends AutomaticCrudData
{

    /**
     * @Assert\NotBlank()
     */
    public ?string $name;

    /**
     * @Assert\NotBlank()
     * @Slug()
     */
    public ?string $slug;

    public ?string $description;

    public ?string $color;

    /**
     * @var Tag $parent
     */
    public ?Tag $parent;

    public function hydrate(): void
    {
        parent::hydrate();
        $this->entity->setUpdatedAt(new \DateTime());
    }
}
