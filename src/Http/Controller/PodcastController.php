<?php

namespace App\Http\Controller;

use App\Domain\Podcast\Repository\PodcastRepository;
use App\Domain\Podcast\Repository\PodcastVoteRepository;
use App\Helper\Paginator\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PodcastController extends AbstractController
{
    /**
     * @Route("/podcast", name="podcast")
     */
    public function index(
        Request $request,
        PodcastRepository $podcastRepository,
        PaginatorInterface $paginator
    ): Response {
        $future = $podcastRepository->findFuture();
        $podcasts = $paginator->paginate($podcastRepository->queryPast()->setMaxResults(11)->getQuery());
        $podcastRepository->hydrateIntervenants((array)$podcasts->getItems());

        return $this->render('podcast/index.html.twig', [
            'page'     => $request->get('page'),
            'future'   => $future,
            'podcasts' => $podcasts,
        ]);
    }

    /**
     * @Route("/podcast/votes", name="podcast_vote")
     */
    public function votes(
        PaginatorInterface $paginator,
        PodcastRepository $podcastRepository,
        PodcastVoteRepository $podcastVoteRepository,
        Request $request
    ): Response {
        $order = $request->get('order', 'popular');
        $podcasts = $paginator->paginate($podcastRepository->querySuggestions($order)->setMaxResults(11)->getQuery());
        $votes = $podcastVoteRepository->podcastIdsForUser($this->getUser());

        return $this->render('podcast/votes.html.twig', [
            'podcasts' => $podcasts,
            'votes'    => $votes,
            'order'    => $order,
            'page'     => $request->get('page')
        ]);
    }
}
