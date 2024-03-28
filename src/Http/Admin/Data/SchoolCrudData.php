<?php

namespace App\Http\Admin\Data;

use App\Domain\Auth\User;
use App\Domain\School\Entity\School;
use App\Validator\Unique;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @property School $entity
 */
#[Unique(field: 'name')]
class SchoolCrudData extends AutomaticCrudData
{
    #[Assert\NotBlank]
    public string $name = '';

    #[Assert\NotBlank]
    public ?string $emailTemplate = '';

    #[Assert\NotBlank]
    public int $credits = 0;

    public ?User $owner;
}
