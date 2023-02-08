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
    #[Assert\NotBlank]
    public ?string $name = null;

    #[Slug]
    #[Assert\NotBlank]
    public ?string $slug = null;

    public ?string $description = null;

    public ?string $color = null;

    public bool $visible = true;

    /**
     * @var Tag
     */
    public ?Tag $parent = null;

    public function hydrate(): void
    {
        parent::hydrate();
        $this->entity->setUpdatedAt(new \DateTime());
    }
}
