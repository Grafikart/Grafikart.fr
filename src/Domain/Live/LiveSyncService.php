<?php

namespace App\Domain\Live;

/**
 * Gère la synchronisation des streams sur le site
 */
class LiveSyncService
{

    private LiveRepository $liveRepository;

    private string $playlistID;

    private \Google_Service_YouTube_Resource_PlaylistItems $playlistItems;

    public function __construct(
        \Google_Service_YouTube_Resource_PlaylistItems $playlistItems,
        LiveRepository $liveRepository,
        string $playlistID
    ) {
        $this->liveRepository = $liveRepository;
        $this->playlistID = $playlistID;
        $this->playlistItems = $playlistItems;
    }

    /**
     * @return Live[]
     */
    public function buildNewLives(): array
    {
        $lastPublishedAt = $this->liveRepository->lastCreationDate();
        $queryParams = [
            'maxResults' => 1,
            'playlistId' => $this->playlistID,
            'part'       => 'contentDetails,snipper,id'
        ];
        $response = $this->playlistItems->listPlaylistItems('snippet', $queryParams);
        $newLives = [];
        /** @var \Google_Service_YouTube_PlaylistItem[] $items */
        $items = $response->getItems();
        foreach ($items as $item) {
            $live = Live::fromPlayListItem($item);
            if ($live->getCreatedAt() > $lastPublishedAt) {
                $newLives[] = $live;
            }
        }

        return array_reverse($newLives);
    }

}
