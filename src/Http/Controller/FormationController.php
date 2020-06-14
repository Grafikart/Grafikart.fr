<?php

namespace App\Http\Controller;

use App\Domain\Course\Entity\Formation;
use App\Domain\Course\Repository\FormationRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormationController extends AbstractController
{

    /**
     * @Route("/formations", name="formation_index")
     */
    public function index(FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findAll();
        return $this->render('formations/index.html.twig', [
            'formations' => $formations
        ]);
    }

    /**
     * @Route("/formations/{slug}", name="formation_show")
     */
    public function show(Formation $formation): Response
    {
        return $this->render('formations/show.html.twig', [
            'formation' => $formation
        ]);
    }

}
