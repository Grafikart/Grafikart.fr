<?php

namespace App\Http\Controller;

use App\Core\Helper\Paginator\PaginatorInterface;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Repository\CourseRepository;
use App\Domain\Course\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TechnologyController extends AbstractController
{

    /**
     * @Route("/tutoriels/{slug}", name="technology_show", requirements={"slug"="[a-z\-]+"})
     */
    public function index(
        Technology $technology,
        FormationRepository $formationRepository,
        CourseRepository $courseRepository,
        PaginatorInterface $paginator,
        Request $request
    ): Response {
        $page = $request->query->getInt('page', 1);
        $nextTechnologies = collect($technology->getRequiredBy())->groupBy(fn(Technology $t) => $t->getType());
        return $this->render('courses/technology.html.twig', [
            'technology' => $technology,
            'formations' => $page !== 1 ? [] : $formationRepository->findForTechnologyPerLevel($technology),
            'courses'    => $paginator->paginate($courseRepository->queryForTechnology($technology)),
            'next' => $nextTechnologies,
            'menu' => 'courses'
        ]);
    }


}
