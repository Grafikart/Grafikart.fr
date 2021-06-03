<?php

namespace App\Http\Controller;

use App\Domain\Podcast\Entity\Podcast;
use App\Domain\Podcast\PodcastService;
use App\Domain\Podcast\Repository\PodcastRepository;
use App\Domain\Podcast\Repository\PodcastVoteRepository;
use App\Helper\Paginator\PaginatorInterface;
use App\Http\Form\PodcastForm;
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
        PodcastService $podcastService,
        Request $request
    ): Response {
        $form = null;
        $user = $this->getUser();
        $order = $request->get('order', 'popular');

        // Traitement du formulaire
        $isSubmitted = false;
        if ($user) {
            $podcast = new Podcast();
            $podcast->setAuthor($user);
            $form = $this->createForm(PodcastForm::class, $podcast);
            $form->handleRequest($request);
            $isSubmitted = $form->isSubmitted();
            if ($isSubmitted && $form->isValid()) {
                $podcastService->suggest($podcast);
                $order = 'date';
                $this->addFlash('success', 'Merci pour votre suggestion de sujet !');
            }
        }

        $podcasts = $paginator->paginate($podcastRepository->querySuggestions($order)->setMaxResults(11)->getQuery());
        $votes = $podcastVoteRepository->podcastIdsForUser($user);

        return $this->render('podcast/votes.html.twig', [
            'podcasts'     => $podcasts,
            'votes'        => $votes,
            'order'        => $order,
            'page'         => $request->get('page'),
            'form'         => $form ? $form->createView() : null,
            'is_submitted' => $isSubmitted
        ]);
    }
}
