<?php

namespace App\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function home(): Response
    {
        return $this->render('pages/home.html.twig');
    }

    /**
     * @Route("/env", name="env")
     */
    public function env(): Response
    {
        return $this->render('pages/env.html.twig');
    }

}
