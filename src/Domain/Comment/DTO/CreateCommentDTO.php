<?php

namespace App\Domain\Comment\DTO;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Validator\Exists;
use App\Validator\NotExists;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class CreateCommentDTO extends UpdateCommentDTO
{

    #[Assert\NotBlank(normalizer: 'trim', groups: ['anonymous'])]
    #[NotExists(class: User::class, groups: ['anonymous'], field: 'username', message: 'Ce pseudo est utilisé par un utilisateur')]
    #[Groups(['anonymous'])]
    public ?string $username = null;

    #[Assert\Positive(groups: ['write'])]
    #[Exists(class: Content::class, groups: ['write'])]
    #[Groups(['write'])]
    public int $target;

    #[Assert\Positive(groups: ['write'])]
    #[Exists(class: Content::class, groups: ['write'])]
    #[Groups(['write'])]
    public ?int $parent = null;

}
