<?php

namespace App\Domain\Profile\Dto;

use App\Domain\Auth\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class AvatarDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Image(mimeTypes: ['image/jpeg', 'image/png'], minWidth: 110, maxHeight: 1400, maxWidth: 1400, minHeight: 110)]
        public UploadedFile $file,
        public User $user
    ) {
    }
}
