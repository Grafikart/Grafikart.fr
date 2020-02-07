<?php

namespace App\Controller;

use App\Domain\Live\LiveRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LiveController extends AbstractController
{
    /**
     * @Route("/live", name="live")
     */
    public function index(LiveRepository $repo): Response
    {
        return $this->render('live/index.html.twig', [
            'menu' => 'live',
            'lives' => $repo->findForYear((int)date('Y')),
        ]);
    }
}
