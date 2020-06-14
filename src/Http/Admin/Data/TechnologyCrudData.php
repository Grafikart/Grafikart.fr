<?php

namespace App\Http\Admin\Data;

use App\Core\Validator\Slug;
use App\Core\Validator\Unique;
use App\Domain\Course\Entity\Technology;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Unique(field="slug")
 * @Unique(field="name")
 * @property Technology $entity
 */
class TechnologyCrudData extends AutomaticCrudData
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

    public ?Collection $requirements = null;

    public ?string $content;

    public ?UploadedFile $imageFile = null;

    public function hydrate(): void
    {
        parent::hydrate();
        $this->entity->setUpdatedAt(new \DateTime());
    }
}
