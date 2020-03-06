<?php

namespace App\Http\Controller;

use App\Domain\Course\Entity\Formation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FormationController extends AbstractController
{

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
