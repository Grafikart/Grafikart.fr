<?php

namespace App\Http\Api\Controller;

use App\Domain\Forum\TopicService;
use App\Http\Controller\AbstractController;
use App\Http\Security\ForumVoter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @method \App\Domain\Auth\User getUser()
 */
#[Route(path: '/forum')]
class ForumController extends AbstractController
{
    #[Route(path: '/read', name: 'forum/read_all', methods: ['POST'])]
    public function readAll(
        TopicService $topicService
    ): JsonResponse {
        $this->denyAccessUnlessGranted(ForumVoter::READ_TOPICS);
        $topicService->readAllTopics($this->getUser());

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
