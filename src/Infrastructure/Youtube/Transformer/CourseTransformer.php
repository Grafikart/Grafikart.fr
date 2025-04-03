<?php

namespace App\Infrastructure\Youtube\Transformer;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Technology;
use Symfony\Component\Serializer\SerializerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

/**
 * Transforme un cours en objet / tableau adapté à l'API Youtube.
 */
class CourseTransformer
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly StorageInterface $storage,
        private readonly string $videosPath,
    ) {
    }

    public function transform(Course $course): \Google_Service_YouTube_Video
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
        $video = new \Google_Service_YouTube_Video();
        $snippet = new \Google_Service_YouTube_VideoSnippet();
        $snippet->setCategoryId('28');
        $snippet->setDescription(sprintf("🔗 Article : {$url}

%s
______________________

Soutenir la chaîne :
⭐ Devenez premium : https://grafikart.fr/premium

Retrouvez Grafikart :
🐦 Twitter : https://twitter.com/grafikart_fr
💬 Discord : https://grafikart.fr/tchat", $course->getExcerpt()));
        $snippet->setTitle($title);
        $snippet->setDefaultAudioLanguage('fr');
        $snippet->setDefaultLanguage('fr');
        $video->setSnippet($snippet);
        $status = new \Google_Service_YouTube_VideoStatus();
        $status->setEmbeddable(true);
        $status->setPublicStatsViewable(false);
        if ($course->getCreatedAt() > new \DateTimeImmutable()) {
            $status->setPrivacyStatus('private');
            $status->setPublishAt($course->getCreatedAt()->format(DATE_ISO8601));
        } else {
            $status->setPrivacyStatus('public');
        }
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
