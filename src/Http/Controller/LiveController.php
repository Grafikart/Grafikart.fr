<?php

namespace App\Http\Controller;

use App\Domain\Live\LiveService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LiveController extends AbstractController
{
    #[Route(path: '/live/{year?}', name: 'live', requirements: ['year' => '\d{4}'])]
    public function index(?int $year, Request $request, LiveService $liveService): Response
    {
        return $this->render('live/index.html.twig', [
            'menu' => 'live',
            'year' => $year,
            'on_air' => $liveService->isLiveRunning(),
            'live_future' => $liveService->getNextLiveDate() > new \DateTimeImmutable(),
            'live_at' => $liveService->getNextLiveDate(),
        ]);
    }
}
