<?php

namespace App\Http\Admin\Data;

use App\Domain\Badge\Entity\Badge;
use App\Validator\Unique;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
    public string $theme = 'grey';

    /**
     * @Assert\NotBlank()
     */
    public int $actionCount = 0;

    public ?UploadedFile $imageFile = null;

    public function hydrate(): void
    {
        parent::hydrate();
        $this->entity->setUpdatedAt(new \DateTimeImmutable());
    }
}
