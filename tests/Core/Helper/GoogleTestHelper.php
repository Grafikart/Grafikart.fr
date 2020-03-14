<?php

namespace App\Tests\Core\Helper;

/**
 * Série de fonctions permettant de générer des objets en liens avec l'API de Google
 */
class GoogleTestHelper
{

    /**
     * Génère un faux PlaylistItem
     *
     * @param array{id?: string, description?: string, title?: string, date?: string, duration?: int} $options
     *
     * ## Exemple d'options
     *
     * - id (string)
     * - description (string)
     * - title (string)
     * - date (string)
     */
    public static function fakeYoutubePlaylistItem(array $options = []): \Google_Service_YouTube_PlaylistItem {
        $item = new \Google_Service_YouTube_PlaylistItem();
        $id = new \Google_Service_YouTube_ResourceId();
        $id->setVideoId($options['id'] ?? 'video');
        $snippet = new \Google_Service_YouTube_PlaylistItemSnippet();
        $contentDetails = new \Google_Service_YouTube_PlaylistItemContentDetails();
        $thumbnails = new \Google_Service_YouTube_ThumbnailDetails();
        $snippet->setDescription($options['description'] ?? 'lorem ipsum');
        $snippet->setTitle($options['title'] ?? 'Title');
        $snippet->setPublishedAt((new \DateTime($options['date'] ?? 'now'))->format('c'));
        $snippet->setThumbnails($thumbnails);
        $contentDetails->setEndAt($options['duration'] ?? 1000);
        $snippet->setResourceId($id);
        $item->setSnippet($snippet);
        $item->setContentDetails($contentDetails);
        return $item;
    }

}
