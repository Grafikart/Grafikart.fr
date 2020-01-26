<?php

namespace App\Services\Api;

class GoogleServiceFactory
{

    private \Google_Client $client;

    public function __construct(\Google_Client $client)
    {
        $this->client = $client;
    }

    public function createPlaylistItems (): \Google_Service_YouTube_Resource_PlaylistItems
    {
        $this->client->setScopes([
            'https://www.googleapis.com/auth/youtube.readonly',
        ]);
        $service = new \Google_Service_YouTube($this->client);
        return $service->playlistItems;
    }

}
