<?php

namespace App\Http\Controller\Course;

use App\Domain\Course\Entity\Cursus;
use App\Domain\Course\Repository\CursusRepository;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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

    /**
     * Route("/cursus/{slug<[a-z0-9\-]+>}", name="cursus_show").
     */
    public function show(Cursus $cursus): Response
    {
        return $this->render('cursus/show.html.twig', [
            'cursus' => $cursus,
        ]);
    }
}
