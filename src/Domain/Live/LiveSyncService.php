<?php

namespace App\Domain\Live;

use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

/**
 * Gère la synchronisation des streams sur le site.
 */
class LiveSyncService
{
    private string $playlistID;
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;
    private \Google_Client $googleClient;

    public function __construct(
        \Google_Client $googleClient,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
        string $playlistID
    ) {
        $this->playlistID = $playlistID;
        $this->em = $em;
        $this->dispatcher = $dispatcher;
        $this->googleClient = $googleClient;
    }

    /**
     * @return Live[]
     */
    public function sync(array $accessToken): array
    {
        /** @var LiveRepository $repository */
        $repository = $this->em->getRepository(Live::class);
        $this->googleClient->setAccessToken($accessToken);
        $youtube = new \Google_Service_YouTube($this->googleClient);
        $lastPublishedAt = $repository->lastCreationDate();
        $queryParams = [
            'maxResults' => 5,
            'playlistId' => $this->playlistID,
        ];

        // On récupère les IDs des vidéos
        /** @var string[] $videos */
        $videos = [];
        /** @var \Google_Service_YouTube_PlaylistItem[] $items */
        $items = $youtube->playlistItems->listPlaylistItems('snippet', $queryParams)->getItems();
        foreach ($items as $item) {
            $publishedAt = new \DateTime($item->getSnippet()->getPublishedAt());
            if ($publishedAt > $lastPublishedAt) {
                $videos[] = $item->getSnippet()->getResourceId()->getVideoId();
            }
        }

        // On récupère les vidéos depuis l'API
        /** @var \Google_Service_YouTube_Video[] $videos */
        $videos = $youtube->videos->listVideos('snippet,contentDetails', [
            'id' => implode(',', array_reverse($videos)),
        ])->getItems();

        // On convertit les vidéos youtube en Live et on les persiste
        $newLives = array_map(fn (\Google_Service_YouTube_Video $video) => Live::fromYoutubeVideo($video), $videos);
        array_map([$this->em, 'persist'], $newLives);
        $this->em->flush();
        foreach ($newLives as $live) {
            $this->dispatcher->dispatch(new LiveCreatedEvent($live));
        }

        return $newLives;
    }
}
