<?php

namespace App\Http\Admin\Data;

use App\Domain\Course\Entity\Technology;
use App\Domain\Forum\Entity\Tag;
use App\Validator\Slug;
use Symfony\Component\Validator\Constraints as Assert;

/**
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

    public bool $visible = true;

    /**
     * @var Tag
     */
    public ?Tag $parent;

    public function hydrate(): void
    {
        parent::hydrate();
        $this->entity->setUpdatedAt(new \DateTime());
    }
}
