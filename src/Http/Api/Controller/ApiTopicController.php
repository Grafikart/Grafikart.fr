<?php

namespace App\Http\Api\Controller;

use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\TopicService;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/forum")
 */
class ApiTopicController extends AbstractController
{

    /**
     * @Route("/topics/{id<\d+>}/follow", name="api_forum/topic_follow", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function toggleFollow(Topic $topic, TopicService $topicService, EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUserOrThrow();
        $isSubscribed = $topicService->isUserSubscribedToTopic($topic, $user);
        if ($isSubscribed === null) {
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
