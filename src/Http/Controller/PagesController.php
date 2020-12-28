<?php

namespace App\Http\Controller;

use App\Domain\Blog\Repository\PostRepository;
use App\Domain\Course\Repository\CourseRepository;
use App\Domain\Course\Repository\CursusRepository;
use App\Domain\Course\Repository\FormationRepository;
use App\Domain\Live\LiveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{

    /**
     * @Route("/a-propos", name="env")
     */
    public function env(): Response
    {
        return $this->render('pages/env.html.twig');
    }

    /**
     * @Route("/politique-de-confidentialite", name="confidentialite")
     */
    public function confidentialite(): Response
    {
        return $this->render('pages/confidentialite.html.twig');
    }

    /**
     * @Route("/ui", name="ui")
     */
    public function ui(): Response
    {
        return $this->render('pages/ui.html.twig');
    }

    /**
     * @Route("/tchat", name="tchat")
     */
    public function tchat(): RedirectResponse
    {
        return new RedirectResponse('https://discordapp.com/invite/rAuuD7Q');
    }
}
