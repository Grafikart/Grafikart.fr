<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Badge\BadgeService;
use App\Domain\Comment\CommentRepository;
use App\Domain\Forum\Repository\TopicRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/profil/{id<\d+>}", name="user_show", requirements={"id"="\d+"})
     */
    public function show(
        User $user,
        TopicRepository $topicRepository,
        CommentRepository $commentRepository,
        BadgeService $badgeService
    ): Response {
        $lastTopics = $topicRepository->findLastByUser($user);
        $badges = $badgeService->getBadges();
        $unlocks = $badgeService->getUnlocksForUser($user);

        return $this->render('user/profil.html.twig', [
            'user' => $user,
            'last_topics' => $lastTopics,
            'comments' => $commentRepository->findLastByUser($user),
            'badges' => $badges,
            'unlocks' => $unlocks,
        ]);
    }
}
