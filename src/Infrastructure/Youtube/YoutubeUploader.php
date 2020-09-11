<?php

namespace App\Infrastructure\Youtube;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use App\Infrastructure\Youtube\Transformer\CourseTransformer;
use Google_Service_YouTube;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;
use Symfony\Component\Serializer\SerializerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

class YoutubeUploader
{
    private Google_Service_YouTube $youtube;
    private string $videoPath;
    private StorageInterface $storage;
    private CourseTransformer $courseTransformer;

    public function __construct(
        Google_Service_YouTube $youtube,
        StorageInterface $storage,
        CourseTransformer $courseTransformer,
        string $videoPath
    ) {
        $newClient = clone $youtube->getClient();
        $newClient->setScopes([
            'https://www.googleapis.com/auth/youtube',
            'https://www.googleapis.com/auth/youtube.upload',
        ]);
        $this->youtube = new Google_Service_YouTube($newClient);
        $this->videoPath = $videoPath;
        $this->storage = $storage;
        $this->courseTransformer = $courseTransformer;
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

        $youtubeId = $course->getYoutubeId();
        $video = $this->courseTransformer->transform($course);
        $parts = 'snippet,status';
        if ($youtubeId) {
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
