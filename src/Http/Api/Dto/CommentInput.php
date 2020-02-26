<?php

namespace App\Http\Api\Dto;

use Symfony\Component\Validator\Constraints as Assert;

final class CommentInput
{

    public int $target;

    /**
     * @Assert\NotBlank(groups={"anonymous"})
     */
    public ?string $username = null;

    /**
     * @Assert\NotBlank(groups={"anonymous"})
     * @Assert\Email(groups={"anonymous"})
     */
    public ?string $email = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="4")
     */
    public string $content;

}
