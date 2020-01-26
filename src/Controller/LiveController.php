<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LiveController extends AbstractController
{
    /**
     * @Route("/live", name="live")
     */
    public function index(EventDispatcherInterface $dispatcher): Response {
        return $this->render('live/index.html.twig', ['controller_name' => 'Live']);
    }
}
