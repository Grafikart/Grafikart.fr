<?php

namespace App\Http\Controller;

use App\Domain\Podcast\Repository\PodcastRepository;
use App\Helper\Paginator\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PodcastController extends AbstractController
{
    /**
     * @Route("/podcast", name="podcast")
     */
    public function index(Request $request, PodcastRepository $podcastRepository, PaginatorInterface $paginator, EntityManagerInterface $em): Response
    {
        $future = $podcastRepository->findFuture();
        $podcasts = $paginator->paginate($podcastRepository->queryPast()->setMaxResults(11)->getQuery());
        $podcastRepository->hydrateIntervenants((array) $podcasts->getItems());

        return $this->render('podcast/index.html.twig', [
            'page' => $request->get('page'),
            'future' => $future,
            'podcasts' => $podcasts,
        ]);
    }
}
