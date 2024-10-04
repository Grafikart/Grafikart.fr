<?php

namespace App\Http\Controller;

use App\Domain\Podcast\Entity\Podcast;
use App\Domain\Podcast\PodcastService;
use App\Domain\Podcast\Repository\PodcastRepository;
use App\Domain\Podcast\Repository\PodcastVoteRepository;
use App\Helper\Paginator\PaginatorInterface;
use App\Http\Form\PodcastForm;
use App\Http\Requirements;
use App\Http\Security\PodcastVoter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class PodcastController extends AbstractController
{
    #[Route(path: '/podcasts', name: 'podcast')]
    public function index(
        Request $request,
        PodcastRepository $podcastRepository,
        PaginatorInterface $paginator
    ): Response {
        $future = $podcastRepository->findFuture();
        $podcasts = $paginator->paginate($podcastRepository->queryPast()->setMaxResults(11)->getQuery());
        $podcastRepository->hydrateIntervenants((array) $podcasts->getItems());

        return $this->render('podcast/index.html.twig', [
            'page' => $request->get('page'),
            'menu' => 'podcasts',
            'future' => $future,
            'podcasts' => $podcasts,
        ]);
    }

    #[Route(path: '/podcasts/{id:podcast}', name: 'podcast_show', requirements: ['id' => Requirements::ID])]
    public function show(Podcast $podcast, PodcastRepository $podcastRepository): Response
    {
        return $this->render('podcast/show.html.twig', [
            'podcast' => $podcast,
            'menu' => 'podcasts',
            'podcasts' => $podcastRepository->findRelative($podcast),
            'bodyClass' => 'podcast-page',
        ]);
    }

    #[Route(path: '/podcasts/votes', name: 'podcast_vote')]
    public function votes(
        PaginatorInterface $paginator,
        PodcastRepository $podcastRepository,
        PodcastVoteRepository $podcastVoteRepository,
        PodcastService $podcastService,
        Request $request,
        AuthorizationCheckerInterface $auth
    ): Response {
        $form = null;
        $user = $this->getUser();
        $order = $request->get('order', 'popular');

        // Traitement du formulaire
        $isSubmitted = false;
        if ($user && $auth->isGranted(PodcastVoter::CREATE)) {
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
            'podcasts' => $podcasts,
            'votes' => $votes,
            'menu' => 'podcasts',
            'order' => $order,
            'page' => $request->get('page'),
            'form' => $form ? $form->createView() : null,
            'is_submitted' => $isSubmitted,
            'limit_per_month' => PodcastService::LIMIT_PER_MONTH,
        ]);
    }

    #[Route(path: '/podcasts.rss', name: 'podcast_rss')]
    public function feed(PodcastRepository $podcastRepository): Response
    {
        $podcasts = $podcastRepository->queryPast()->setMaxResults(11)->getQuery()->getResult();

        return $this->render('feed/podcasts.xml.twig', [
            'podcasts' => $podcasts,
        ]);
    }
}
