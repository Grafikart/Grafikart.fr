<?php

namespace App\Tests\Helper;

/**
 * Série de fonctions permettant de générer des objets en lients avec l'API de Google
 */
class GoogleTestHelper
{

    /**
     * Génère un faux PlaylistItem
     *
     * @param array{id?: string, description?: string, title?: string, date?: string} $options
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
        $thumbnails = new \Google_Service_YouTube_ThumbnailDetails();
        $snippet->setDescription($options['description'] ?? 'lorem ipsum');
        $snippet->setTitle($options['title'] ?? 'Title');
        $snippet->setPublishedAt((new \DateTime($options['date'] ?? 'now'))->format('c'));
        $snippet->setThumbnails($thumbnails);
        $snippet->setResourceId($id);
        $item->setSnippet($snippet);
        return $item;
    }

}
