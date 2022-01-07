<?php

namespace App\Domain\Podcast\EventListener;

use App\Domain\Podcast\Entity\Podcast;
use App\Helper\PathHelper;
use App\Infrastructure\Storage\VideoMetaReader;

class PodcastDurationUpdater
{
    public function __construct(private string $podcastsPath, private VideoMetaReader $metaReader)
    {
    }

    public function updateDuration(Podcast $podcast): void
    {
        if (!empty($podcast->getMp3())) {
            $mp3 = PathHelper::join($this->podcastsPath, $podcast->getMp3());
            if ($duration = $this->metaReader->getDuration($mp3)) {
                $podcast->setDuration($duration);
            }
        }
    }
}
