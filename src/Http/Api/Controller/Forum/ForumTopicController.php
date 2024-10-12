<?php

namespace App\Http\Api\Controller\Forum;

use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\TopicService;
use App\Http\Controller\AbstractController;
use App\Http\Security\ForumVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/forum', name: 'forum_')]
class ForumTopicController extends AbstractController
{
    #[Route(path: '/topics/{topic}', name: 'forum_topic', methods: ['DELETE'])]
    #[IsGranted(ForumVoter::DELETE_TOPIC, subject: 'topic')]
    public function delete(Topic $topic, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($topic);
        $em->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/topics/{topic}/follow', name: 'topic_follow', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function toggleFollow(Topic $topic, TopicService $topicService, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUserOrThrow();
        $isSubscribed = $topicService->isUserSubscribedToTopic($topic, $user);
        if (null === $isSubscribed) {
            return new JsonResponse(['title' => "Impossible de s'abonner au sujet", 'detail' => 'Vous ne participez pas à ce sujet'], Response::HTTP_FORBIDDEN);
        }
        foreach ($topic->getMessages() as $message) {
            if ($message->getAuthor()->getId() === $user->getId()) {
                $message->setNotification(!$isSubscribed);
            }
        }
        $em->flush();

        return new JsonResponse(['subscribed' => !$isSubscribed]);
    }
}
