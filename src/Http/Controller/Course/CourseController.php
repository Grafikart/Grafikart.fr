<?php

namespace App\Http\Controller\Course;

use App\Domain\Course\Entity\Course;
use const App\Domain\Course\Entity\EASY;
use const App\Domain\Course\Entity\HARD;
use App\Domain\Course\Repository\CourseRepository;
use App\Domain\Course\Repository\TechnologyRepository;
use App\Helper\Paginator\PaginatorInterface;
use App\Http\Security\CourseVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Storage\StorageInterface;

class CourseController extends AbstractController
{
    /**
     * @Route("/tutoriels", name="course_index")
     */
    public function index(CourseRepository $repo, PaginatorInterface $paginator, Request $request, TechnologyRepository $technologyRepository): Response
    {
        $query = $repo->queryAll();
        $page = $request->query->getInt('page', 1);

        // On filtre par niveau
        $level = $request->query->get('level');
        $levelInt = (int) $level;
        if (null !== $level && ((string) $levelInt !== $level || (int) $level < EASY || (int) $level > HARD)) {
            throw new BadRequestHttpException();
        }
        $levels = Course::$levels;
        if (null !== $level) {
            $query = $query->setParameter('level', $levelInt)->andWhere('c.level = :level');
        }

        // On filtre sur une technology
        $technologySlug = $request->query->get('technology');
        $technology = null;
        if ($technologySlug) {
            $technology = $technologyRepository->findOneBy(['slug' => $technologySlug]);
            if (null !== $technology) {
                $query = $query->setParameter('technology', $technology)->leftJoin('c.technologyUsages', 'tu')->andWhere('tu.technology = :technology');
            }
        }

        $courses = $paginator->paginate($query->setMaxResults(26)->getQuery());

        return $this->render('courses/index.html.twig', [
            'courses' => $courses,
            'page' => $page,
            'level' => $levels[$level] ?? null,
            'levels' => $levels,
            'menu' => 'courses',
            'technology_selected' => $technology,
            'technologies' => $technologyRepository->findByType(),
        ], new Response('', $courses->count() > 0 ? 200 : 404));
    }

    /**
     * @Route("/tutoriels/premium", name="course_premium")
     */
    public function premium(CourseRepository $repo, PaginatorInterface $paginator, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $courses = $paginator->paginate($repo->queryAllPremium()->setMaxResults(26)->getQuery());
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
     * @Route("/tutoriels/{slug<[a-z0-9A-Z\-]+>}-{id<\d+>}", priority=10, name="course_show")
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
        $this->denyAccessUnlessGranted(CourseVoter::DOWNLOAD_SOURCE);
        if (null === $course->getSource()) {
            throw new NotFoundHttpException();
        }

        $path = $storage->resolvePath($course, 'sourceFile', null, true);

        return $this->redirectToRoute('download_source', ['source' => $path]);
    }

    /**
     * @Route("/tutoriels/{id<\d+>}/video", name="course_download_video")
     */
    public function downloadVideo(Course $course, StorageInterface $storage): Response
    {
        $this->denyAccessUnlessGranted(CourseVoter::DOWNLOAD_VIDEO, $course);

        return $this->redirectToRoute('stream_video', ['video' => $course->getVideoPath()]);
    }

    /**
     * Redirection des anciennes URLs vers la nouvelle.
     *
     * @Route("/tutoriels/{technology<[a-z0-9A-Z\-]+>}/{slug<[a-z0-9A-Z\-]+>}-{id<\d+>}", name="legacy_course_show")
     */
    public function legacyShow(string $technology, string $slug, string $id): Response
    {
        return $this->redirectToRoute('course_show', ['slug' => $slug, 'id' => $id], Response::HTTP_MOVED_PERMANENTLY);
    }
}
