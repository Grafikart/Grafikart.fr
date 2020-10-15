<?php

namespace App\Http\Controller\Course;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Repository\CourseRepository;
use App\Http\Security\CourseVoter;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Storage\StorageInterface;

class CourseController extends AbstractController
{
    /**
     * @Route("/tutoriels", name="course_index")
     */
    public function index(CourseRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $courses = $paginator->paginate(
            $repo->queryAll(),
            $page,
            26,
            [
                'whiteList' => [],
            ]
        );
        if (0 === $courses->count()) {
            throw new NotFoundHttpException('Aucun tutoriels ne correspond à cette page');
        }

        return $this->render('courses/index.html.twig', [
            'courses' => $courses,
            'page' => $page,
            'menu' => 'courses',
        ]);
    }

    /**
     * @Route("/tutoriels/premium", name="course_premium")
     */
    public function premium(CourseRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $courses = $paginator->paginate(
            $repo->queryAllPremium(),
            $page,
            26,
            [
                'whiteList' => [],
            ]
        );
        if (0 === $courses->count()) {
            throw new NotFoundHttpException('Aucun tutoriels ne correspond à cette page');
        }

        return $this->render('courses/premium.html.twig', [
            'courses' => $courses,
            'page' => $page,
            'menu' => 'courses',
        ]);
    }

    /**
     * @Route("/tutoriels/{slug<[a-z0-9A-Z\-]+>}-{id<\d+>}", name="course_show")
     */
    public function show(Course $course, string $slug): Response
    {
        if ($course->getSlug() !== $slug) {
            return $this->redirectToRoute('course_show', [
                'id' => $course->getId(),
                'slug' => $course->getSlug(),
            ], 301);
        }

        return $this->render('courses/show.html.twig', [
            'course' => $course,
            'menu' => 'courses',
        ]);
    }

    /**
     * @Route("/tutoriels/{id<\d+>}/sources", name="course_download_source")
     */
    public function downloadSource(Course $course, StorageInterface $storage): Response
    {
        $this->denyAccessUnlessGranted(CourseVoter::DOWNLOAD_SOURCE, $course);
        if (null === $course->getSource()) {
            throw new NotFoundHttpException();
        }
        /** @var string $filePath */
        $filePath = $storage->resolvePath($course, 'sourceFile');
        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $course->getTitle().'.zip');

        return $response;
    }

    /**
     * @Route("/tutoriels/{id<\d+>}/video", name="course_download_video")
     */
    public function downloadVideo(Course $course, StorageInterface $storage): Response
    {
        $this->denyAccessUnlessGranted(CourseVoter::DOWNLOAD_VIDEO, $course);
        $videoPath = rtrim($this->getParameter('videos_path'), '/').'/'.$course->getVideoPath();
        $extension = pathinfo($videoPath, PATHINFO_EXTENSION);
        $response = new BinaryFileResponse($videoPath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, "{$course->getTitle()}.{$extension}");

        return $response;
    }
}
