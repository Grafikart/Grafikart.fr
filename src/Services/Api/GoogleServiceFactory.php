<?php

namespace App\Services\Api;

class GoogleServiceFactory
{
    public static function createPlaylistItems (\Google_Client $client): \Google_Service_YouTube_Resource_PlaylistItems
    {
        $client->setScopes([
            'https://www.googleapis.com/auth/youtube.readonly',
        ]);
        $service = new \Google_Service_YouTube($client);
        return $service->playlistItems;
    }

}
