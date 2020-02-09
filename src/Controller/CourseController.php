<?php

namespace App\Controller;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Repository\CourseRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

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
            26
        );
        if ($courses->count() === 0) {
            throw new NotFoundHttpException('Aucun tutoriels ne correspond à cette page');
        }
        return $this->render('courses/index.html.twig', [
            'courses' => $courses,
            'page' => $page,
            'menu' => 'courses'
        ]);
    }

    /**
     * @Route("/tutoriels/{slug<[a-z0-9\-]+>}-{id<\d+>}", name="course_show")
     */
    public function show(Course $course, string $slug): Response
    {
        if ($course->getSlug() !== $slug) {
            return $this->redirectToRoute('course_show', [
                'id' => $course->getId(),
                'slug' => $course->getSlug()
            ], 301);
        }
        return $this->render('courses/show.html.twig', [
            'course' => $course,
            'menu' => 'courses'
        ]);
    }
}
