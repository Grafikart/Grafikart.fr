<?php

namespace App\Http\Api\Controller;

use App\Domain\Auth\User;
use App\Domain\Course\Entity\Course;
use App\Domain\Premium\VideoUrlGenerator;
use App\Http\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method User getUser()
 */
class VideoController extends AbstractController
{
    /**
     * @Route("/video/{id}", name="video_url", methods={"POST"})
     * @IsGranted("STREAM_COURSE", subject="course")
     */
    public function video(Course $course, VideoUrlGenerator $urlGenerator): JsonResponse
    {
        $videoPath = $course->getVideoPath();
        if (!$course->isVideoPremium() || null === $videoPath) {
            throw new NotFoundHttpException();
        }

        return $this->json([
            'url' => $urlGenerator->generate($videoPath, $this->getUserOrThrow()),
        ]);
    }
}
