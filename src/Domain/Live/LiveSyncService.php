<?php

namespace App\Domain\Live;

use Google_Service_YouTube;

/**
 * Gère la synchronisation des streams sur le site
 */
class LiveSyncService
{

    private \Google_Client $client;
    private LiveRepository $liveRepository;
    private string $playlistID;

    public function __construct(
        \Google_Client $client,
        LiveRepository $liveRepository,
        string $playlistID
    ) {
        $this->client = $client;
        $this->liveRepository = $liveRepository;
        $this->playlistID = $playlistID;
    }

    /**
     * @return Live[]
     */
    public function buildNewLives(): array
    {
        $this->client->setScopes([
            'https://www.googleapis.com/auth/youtube.readonly',
        ]);
        $lastPublishedAt = $this->liveRepository->lastCreationDate();
        $service = new Google_Service_YouTube($this->client);
        $queryParams = [
            'maxResults' => 50,
            'playlistId' => $this->playlistID,
        ];
        $response = $service->playlistItems->listPlaylistItems('snippet', $queryParams);
        $newLives = [];
        /** @var \Google_Service_YouTube_PlaylistItem $item */
        foreach ($response->getItems() as $item) {
            $live = Live::fromPlayListItem($item);
            if ($live->getCreatedAt() > $lastPublishedAt) {
                $newLives[] = $live;
            }
        }
        return $newLives;
    }

}
