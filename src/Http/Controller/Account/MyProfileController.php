<?php

namespace App\Http\Controller\Account;

use App\Domain\Forum\Repository\TopicRepository;
use App\Domain\History\HistoryService;
use App\Domain\Revision\RevisionRepository;
use App\Http\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyProfileController extends AbstractController
{
    #[Route(path: '/profil', name: 'user_profil', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        HistoryService $history,
        TopicRepository $topicRepository,
        RevisionRepository $revisionRepository
    ): Response {
        $user = $this->getUserOrThrow();
        $revisions = $revisionRepository->findPendingFor($user);
        $watchlist = $history->getLastWatchedContent($user);
        $lastTopics = $topicRepository->findLastByUser($user);
        $lastMessageTopics = $topicRepository->findLastWithUser($user);
        $hasActivity = !empty($lastTopics) || !empty($lastMessageTopics) || !empty($watchlist) || !empty($revisions);

        return $this->render('account/index.twig', [
            'watchlist' => $watchlist,
            'lastTopics' => $lastTopics,
            'revisions' => $revisions,
            'menu' => 'account',
            'lastMessageTopics' => $lastMessageTopics,
            'hasActivity' => $hasActivity,
        ]);
    }
}
