<?php

namespace App\Controller;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CourseController extends AbstractController
{

    /**
     * @Route("/tutoriels", name="course_index")
     */
    public function index(CourseRepository $repo): Response
    {
        $courses = $repo->paginateAll();
        return $this->render('courses/index.html.twig', [
            'courses' => $courses,
            'menu' => 'courses'
        ]);
    }

    /**
     * @Route("/tutoriels/{slug<[a-z0-9\-]+>}-{id<\d+>}", name="course_show")
     */
    public function show(Course $course, string $slug): Response
    {
        if ($course->getSlug() !== $slug) {
            $this->redirectToRoute('course_show', [
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
