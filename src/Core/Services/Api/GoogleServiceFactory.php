<?php

namespace App\Core\Services\Api;

class GoogleServiceFactory
{
    public static function getYoutubeService(\Google_Client $client): \Google_Service_YouTube
    {
        $client->setScopes([
            'https://www.googleapis.com/auth/youtube.readonly',
        ]);

        return new \Google_Service_YouTube($client);
    }
}
