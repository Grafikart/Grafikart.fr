<?php

namespace App\Domains\Live;

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

    public function persist(Live $live)
    {
        $thumbnail = @fopen($live->getYoutubeThumbnail(), 'r');
        if ($thumbnail === false) {
            $thumbnail = fopen(str_replace('maxresdefault', 'default', $live->getYoutubeThumbnail()), 'r');
        }
        $this->fs->writeStream($live->getThumbnailPath(), $thumbnail);
    }
}
