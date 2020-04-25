<?php

namespace App\Domain\Profile;

use App\Domain\Profile\Dto\AvatarDto;
use Intervention\Image\ImageManager;

class ProfileService
{

    public function updateAvatar(AvatarDto $data): void
    {
        if ($data->file->getRealPath() === false) {
            throw new \RuntimeException('Impossible de redimensionner un avatar non existant');
        }
        // On redimensionne l'image
        $manager = new ImageManager(['driver' => 'imagick']);
        $manager->make($data->file)->fit(110, 110)->save($data->file->getRealPath());

        // On la déplace dans le profil utilisateur
        $data->user->setAvatarFile($data->file);
        $data->user->setUpdatedAt(new \DateTime());
    }

}
