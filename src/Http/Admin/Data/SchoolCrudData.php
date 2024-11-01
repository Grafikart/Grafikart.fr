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

    public string $emailMessage = '';

    public string $emailSubject = 'Compte premium Grafikart.fr';

    #[Assert\NotBlank]
    public ?string $couponPrefix = 'ECOLE';

    #[Assert\NotBlank]
    public int $credits = 0;

    public ?User $owner = null;

    public function setEmailMessage(?string $message): void
    {
        $this->emailMessage = $message ?? '';
    }

    public function setEmailSubject(?string $subject): void
    {
        $this->emailSubject = $subject ?? 'Compte premium Grafikart.fr';
    }
}
