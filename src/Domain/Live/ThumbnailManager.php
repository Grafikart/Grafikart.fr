<?php

namespace App\Domain\Live;

use League\Flysystem\FilesystemInterface;

class ThumbnailManager
{

    /**
     * @var FilesystemInterface
     */
    private FilesystemInterface $fs;

    public function __construct(FilesystemInterface $fs)
    {
        $this->fs = $fs;
    }

    /**
     * Persiste l'image associé à un live
     *
     * @return bool True en cas de succès, False sinon
     *
     * @throws \League\Flysystem\FileExistsException
     */
    public function persist(Live $live): bool
    {
        $thumbnail = @fopen($live->getYoutubeThumbnail(), 'r');
        if ($thumbnail === false) {
            $thumbnail = @fopen(str_replace('maxresdefault', 'default', $live->getYoutubeThumbnail()), 'r');
        }
        if ($thumbnail === false) {
            return false;
        }
        return $this->fs->writeStream($live->getThumbnailPath(), $thumbnail, ['disable_asserts' => true]);
    }
}
