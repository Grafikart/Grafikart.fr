<?php

namespace App\Infrastructure\Youtube;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use Google_Service_YouTube;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;
use Symfony\Component\Serializer\SerializerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

class YoutubeUploader
{
    private Google_Service_YouTube $youtube;
    private SerializerInterface $serializer;
    private string $videoPath;

    private StorageInterface $storage;

    public function __construct(
        Google_Service_YouTube $youtube,
        SerializerInterface $serializer,
        StorageInterface $storage,
        string $videoPath
    ) {
        $newClient = clone $youtube->getClient();
        $newClient->setScopes([
            'https://www.googleapis.com/auth/youtube',
            'https://www.googleapis.com/auth/youtube.upload',
        ]);
        $this->youtube = new Google_Service_YouTube($newClient);
        $this->serializer = $serializer;
        $this->videoPath = $videoPath;
        $this->storage = $storage;
    }

    public function setRedirectUri(string $url): void
    {
        $this->youtube->getClient()->setRedirectUri($url);
    }

    public function getAuthUrl(): string
    {
        return $this->youtube->getClient()->createAuthUrl();
    }

    public function upload(Course $course, string $authCode): string
    {
        // On génère un accessToken
        $accessToken = $this->youtube->getClient()->fetchAccessTokenWithAuthCode($authCode);
        $this->youtube->getClient()->setAccessToken($accessToken);

        // On construit les informations à envoyer
        $url = $this->serializer->serialize($course, 'path', ['url' => true]);
        $title = preg_replace('/[<>]/', '', $course->getTitle() ?: '');
        $formation = $course->getFormation();
        if ($formation) {
            $title = "{$formation->getTitle()} : {$title}";
        } else {
            $technologies = collect($course->getMainTechnologies())->map(fn (Technology $t) => $t->getName())->join('/');
            $title = "Tutoriel {$technologies} : {$title}";
        }
        $youtubeId = $course->getYoutubeId();

        // On crée l'objet Vidéo
        $video = new Google_Service_YouTube_Video();
        $snippet = new Google_Service_YouTube_VideoSnippet();
        $snippet->setCategoryId('28');
        $snippet->setDescription("Article ► {$url}
Abonnez-vous ► https://bit.ly/GrafikartSubscribe

{$course->getExcerpt()}

Soutenez Grafikart:
Devenez premium ► https://grafikart.fr/premium
Donnez via Utip ► https://utip.io/grafikart

Retrouvez Grafikart sur:
Le site ► https://grafikart.fr
Twitter ► https://twitter.com/grafikart_fr
Discord ► https://grafikart.fr/tchat");
        $snippet->setTitle($title);
        $snippet->setDefaultAudioLanguage('fr');
        $snippet->setDefaultLanguage('fr');
        $video->setSnippet($snippet);
        $status = new Google_Service_YouTube_VideoStatus();
        $status->setPrivacyStatus($course->getCreatedAt() > new \DateTimeImmutable() ? 'private' : 'public');
        $status->setEmbeddable(true);
        $status->setPublicStatsViewable(false);
        $status->setPublishAt($course->getCreatedAt()->format(DATE_ISO8601));
        $video->setStatus($status);

        // On upload la vidéo
        $parts = 'snippet,status';
        if ($youtubeId) {
            $video->setId($youtubeId);
            $video = $this->youtube->videos->update($parts, $video);
        } else {
            $data = [
                'data' => file_get_contents($this->videoPath.'/'.$course->getVideoPath()),
                'mimeType' => 'application/octet-stream',
                'uploadType' => 'multipart',
            ];
            $video = $this->youtube->videos->insert($parts, $video, $data);
        }

        // On met à jour la thumbnail
        $youtubeThumbnail = $course->getYoutubeThumbnail();
        $thumbnailPath = $youtubeThumbnail ? $this->storage->resolvePath($youtubeThumbnail, 'file') : null;
        if ($thumbnailPath) {
            $this->youtube->thumbnails->set($video->getId(), [
                'data' => file_get_contents($thumbnailPath),
                'mimeType' => 'application/octet-stream',
                'uploadType' => 'multipart',
            ]);
        }

        return $video->getId();
    }
}
