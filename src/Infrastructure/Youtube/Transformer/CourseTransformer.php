<?php

namespace App\Infrastructure\Youtube\Transformer;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use Google_Service_YouTube_Video;
use Google_Service_YouTube_VideoSnippet;
use Google_Service_YouTube_VideoStatus;
use Symfony\Component\Serializer\SerializerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * Transforme un cours en objet / tableau adapté à l'API Youtube.
 */
class CourseTransformer
{
    private SerializerInterface $serializer;
    private StorageInterface $storage;
    private string $videosPath;

    public function __construct(
        SerializerInterface $serializer,
        StorageInterface $storage,
        string $videosPath
    ) {
        $this->serializer = $serializer;
        $this->storage = $storage;
        $this->videosPath = $videosPath;
    }

    public function transform(Course $course): Google_Service_YouTube_Video
    {
        // On construit les informations à envoyer
        $youtubeId = $course->getYoutubeId();
        $url = $this->serializer->serialize($course, 'path', ['url' => true]);
        $title = preg_replace('/[<>]/', '', $course->getTitle() ?: '');
        $formation = $course->getFormation();
        if ($formation) {
            $title = "{$formation->getTitle()} : {$title}";
        } else {
            $technologies = collect($course->getMainTechnologies())->map(fn (Technology $t) => $t->getName())->join('/');
            $title = "Tutoriel {$technologies} : {$title}";
        }

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
        if ($youtubeId) {
            $video->setId($youtubeId);
        }

        return $video;
    }

    public function videoData(Course $course): array
    {
        return [
            'data' => file_get_contents($this->videosPath.'/'.$course->getVideoPath()),
            'mimeType' => 'application/octet-stream',
            'uploadType' => 'multipart',
        ];
    }

    public function thumbnailData(Course $course): array
    {
        $thumbnail = $course->getYoutubeThumbnail();
        if (null === $thumbnail) {
            throw new \RuntimeException('Impossible de résoudre la miniature pour cette vidéo');
        }
        $thumbnailPath = $this->storage->resolvePath($thumbnail, 'file');
        if (null === $thumbnailPath) {
            throw new \RuntimeException('Impossible de résoudre la miniature pour cette vidéo');
        }

        return [
            'data' => file_get_contents($thumbnailPath),
            'mimeType' => 'application/octet-stream',
            'uploadType' => 'multipart',
        ];
    }
}
