<?php

namespace App\Http\Controller\Course;

use App\Domain\Course\Entity\Formation;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Repository\CourseRepository;
use App\Domain\Course\Repository\FormationRepository;
use App\Helper\Paginator\PaginatorInterface;
use App\Http\Requirements;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TechnologyController extends AbstractController
{
    #[Route(path: '/tutoriels/{slug:technology}', name: 'technology_show', requirements: ['slug' => Requirements::SLUG])]
    public function index(
        Technology $technology,
        FormationRepository $formationRepository,
        CourseRepository $courseRepository,
        PaginatorInterface $paginator,
        Request $request,
    ): Response {
        $page = $request->query->getInt('page', 1);
        $formations = [];
        $formationsPerLevel = [];
        if ($page <= 1) {
            $formations = $formationRepository->findForTechnology($technology);
            $formationsPerLevel = collect($formations)->groupBy(fn (Formation $t) => $t->getLevel())->toArray();
        }
        $nextTechnologies = collect($technology->getRequiredBy())->groupBy(fn (Technology $t) => $t->getType() ?? '');
        $courses = $paginator->paginate($courseRepository->queryForTechnology($technology));

        $isEmpty = count($formations) === 0 && $courses->getTotalItemCount() === 0;

        return $this->render('courses/technology.html.twig', [
            'technology' => $technology,
            'showTabs' => count($formations) > 3,
            'formations' => $formations,
            'formationsPerLevel' => $formationsPerLevel,
            'courses' => $courses,
            'next' => $nextTechnologies,
            'isEmpty' => $isEmpty,
            'menu' => 'courses',
        ]);
    }
}
