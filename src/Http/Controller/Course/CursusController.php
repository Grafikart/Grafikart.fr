<?php

namespace App\Http\Controller\Course;

use App\Domain\Course\Entity\Cursus;
use App\Domain\Course\Repository\CursusRepository;
use App\Http\Controller\AbstractController;
use App\Http\Requirements;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CursusController extends AbstractController
{
    /**
     * Route("/cursus", name="cursus_index").
     */
    public function index(CursusRepository $cursusRepository): Response
    {
        return $this->render('cursus/index.html.twig', [
            'cursus_categories' => $cursusRepository->findByCategory(),
        ]);
    }

    #[Route(path: '/cursus/{slug:cursus}', name: 'cursus_show', requirements: ['slug' => Requirements::SLUG])]
    public function show(Cursus $cursus): Response
    {
        return $this->render('cursus/show.html.twig', [
            'cursus' => $cursus,
        ]);
    }
}
