<?php

namespace App\Http\Api\Controller\Forum;

use App\Domain\Forum\TopicService;
use App\Http\Controller\AbstractController;
use App\Http\Security\ForumVoter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @method \App\Domain\Auth\User getUser()
 */

#[Route(path: '/forum', name: 'forum_')]
class ForumController extends AbstractController
{
    #[Route(path: '/read', name: 'read_all', methods: ['POST'])]
    public function readAll(
        TopicService $topicService,
    ): JsonResponse {
        $this->denyAccessUnlessGranted(ForumVoter::READ_TOPICS);
        $topicService->readAllTopics($this->getUser());

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
