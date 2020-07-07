<?php

namespace App\Http\Controller;

use App\Domain\Live\LiveRepository;
use App\Domain\Live\LiveService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LiveController extends AbstractController
{
    /**
     * @Route("/live/{year?}", name="live")
     */
    public function index(LiveRepository $repo, ?int $year, Request $request, LiveService $liveService): Response
    {
        $year = $year ?: (int)date('Y');
        $lives = $repo->findForYear($year);
        if ($request->get('ajax')) {
            return $this->render('live/year.html.twig', [
                'lives' => $lives
            ]);
        } else {
            $live = $liveService->getCurrentLive();
            return $this->render('live/index.html.twig', [
                'menu'  => 'live',
                'year'  => $year,
                'years' => $repo->findYears(),
                'lives' => $lives,
                'live'  => $live,
                'on_air' => $live && $live->getCreatedAt() <= new \DateTime()
            ]);
        }
    }
}
