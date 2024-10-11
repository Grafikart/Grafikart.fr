<?php

namespace App\Domain\Comment\DTO;

use App\Domain\Application\Entity\Content;
use App\Domain\Comment\Entity\Comment;
use App\Validator\Exists;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

class UpdateCommentDTO
{

    #[Assert\NotBlank(normalizer: 'trim',groups: ['write'])]
    #[Assert\Length(min: 4, max: 1_000, normalizer: 'trim',groups: ['write'])]
    #[Groups(['write'])]
    public string $content = '';

    public Comment $comment;

}
