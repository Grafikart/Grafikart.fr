<?php

namespace App\Controller;

use App\Domain\Live\LiveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LiveController extends AbstractController
{
    /**
     * @Route("/live/{year?}", name="live")
     */
    public function index(LiveRepository $repo, ?int $year, Request $request): Response
    {
        $year = $year ?: (int)date('Y');
        $lives = $repo->findForYear($year);
        if ($request->get('ajax')) {
            return $this->render('live/year.html.twig', [
                'lives' => $lives
            ]);
        } else {
            return $this->render('live/index.html.twig', [
                'menu' => 'live',
                'year' => $year,
                'years' => $repo->findYears(),
                'lives' => $lives,
            ]);
        }
    }
}
