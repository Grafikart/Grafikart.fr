<?php

namespace App\Controller;

use App\Domain\Course\Entity\Technology;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TechnologyController extends AbstractController
{

    /**
     * @Route("/tutoriels/{slug}", name="technology_index", requirements={"slug"="[a-z\-]+"})
     */
    public function index(Technology $technology): Response
    {
        return $this->render('courses/technology.html.twig', [
            'technology' => $technology,
            'menu' => 'courses'
        ]);
    }


}
