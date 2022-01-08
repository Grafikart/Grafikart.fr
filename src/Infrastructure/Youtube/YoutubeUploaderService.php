<?php

namespace App\Infrastructure\Youtube;

use App\Domain\Course\Entity\Course;
use App\Infrastructure\Youtube\Transformer\CourseTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Google_Service_YouTube;

class YoutubeUploaderService
{
    public function __construct(
        private readonly \Google_Client $googleClient,
        private readonly EntityManagerInterface $em,
        private readonly CourseTransformer $transformer
    ) {
    }

    public function upload(int $courseId, array $accessToken): string
    {
        $course = $this->em->getRepository(Course::class)->find($courseId);
        if (null === $course) {
            throw new \RuntimeException("Impossible de trouver le cours #{$courseId}");
        }
        $this->googleClient->setAccessToken($accessToken);
        $youtube = new Google_Service_YouTube($this->googleClient);
        $youtubeId = $course->getYoutubeId();
        $video = $this->transformer->transform($course);
        $parts = 'snippet,status';
        if ($youtubeId) {
            $video = $youtube->videos->update($parts, $video);
        } else {
            $video = $youtube->videos->insert($parts, $video, $this->transformer->videoData($course));
            $course->setYoutubeId($video->getId());
            $this->em->flush();
        }

        // On met à jour la thumbnail
        $youtube->thumbnails->set($video->getId(), $this->transformer->thumbnailData($course));

        return $video->getId();
    }
}
