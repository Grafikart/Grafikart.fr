<?php

namespace App\Http\Controller\Course;

use App\Domain\Course\Entity\Cursus;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CursusController extends AbstractController
{
    /**
     * @Route("/cursus/{slug<[a-z0-9\-]+>}", name="cursus_show")
     */
    public function show(Cursus $cursus): Response
    {
        return $this->render('cursus/show.html.twig', [
            'cursus' => $cursus,
        ]);
    }
}
