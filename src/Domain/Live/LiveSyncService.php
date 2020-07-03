<?php

namespace App\Domain\Live;

/**
 * Gère la synchronisation des streams sur le site
 */
class LiveSyncService
{

    private LiveRepository $liveRepository;

    private string $playlistID;
    private \Google_Service_YouTube $service;

    public function __construct(
        \Google_Service_YouTube $service,
        LiveRepository $liveRepository,
        string $playlistID
    ) {
        $this->service = $service;
        $this->liveRepository = $liveRepository;
        $this->playlistID = $playlistID;
    }

    /**
     * @return Live[]
     */
    public function buildNewLives(): array
    {
        $lastPublishedAt = $this->liveRepository->lastCreationDate();
        $queryParams = [
            'maxResults' => 50,
            'playlistId' => $this->playlistID,
        ];

        // On récupère les IDs des vidéos
        /** @var string[] $videos */
        $videos = [];
        /** @var \Google_Service_YouTube_PlaylistItem[] $items */
        $items = $this->service->playlistItems->listPlaylistItems('snippet', $queryParams)->getItems();
        foreach ($items as $item) {
            $publishedAt = new \DateTime($item->getSnippet()->getPublishedAt());
            if ($publishedAt > $lastPublishedAt) {
                $videos[] = $item->getSnippet()->getResourceId()->getVideoId();
            }
        }

        // On récupère les vidéos depuis l'API
        /** @var \Google_Service_YouTube_Video[] $videos */
        $videos = $this->service->videos->listVideos('snippet,contentDetails', [
            'id' => implode(',', array_reverse($videos))
        ])->getItems();

        // On convertit les vidéos youtube en Live
        return array_map(fn (\Google_Service_YouTube_Video $video) => Live::fromYoutubeVideo($video), $videos);
    }

}
