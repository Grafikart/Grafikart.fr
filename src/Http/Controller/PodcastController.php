<?php

namespace App\Http\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PodcastController extends AbstractController
{

    /**
     * @Route("/podcast", name="podcast")
     */
    public function index(Request $request): Response
    {
        return $this->render('podcast/index.html.twig', [
            'page' => $request->get('page')
        ]);
    }

}
