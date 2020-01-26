<?php

namespace App\Domain\Live;

use League\Flysystem\FilesystemInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Permet de gérer la persistence des fichiers lors de le création d'un évènement
 */
class ThumbnailPersister implements EventSubscriberInterface
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
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LiveCreatedEvent::class => 'persistThumbnail'
        ];
    }

    /**
     * Persiste l'image associé à un live
     *
     * @return bool True en cas de succès, False sinon
     */
    public function persistThumbnail(LiveCreatedEvent $event): bool
    {
        $live = $event->getLive();
        $thumbnail = @fopen($live->getYoutubeThumbnail(), 'r');
        if ($thumbnail === false) {
            $thumbnail = @fopen(str_replace('maxresdefault', 'default', $live->getYoutubeThumbnail()), 'r');
        }
        if ($thumbnail === false) {
            return false;
        }
        $path = $live->getThumbnailPath();
        if ($this->fs->has($path)) {
            $this->fs->delete($path);
        }
        return $this->fs->writeStream($path, $thumbnail);
    }
}
