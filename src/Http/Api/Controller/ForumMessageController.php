<?php

namespace App\Http\Api\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Event\MessageCreatedEvent;
use App\Http\Controller\AbstractController;
use App\Http\Security\ForumVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/forum")
 *
 * @method \App\Domain\Auth\User getUser()
 */
class ForumMessageController extends AbstractController
{
    /**
     * @Route("/topics/{id}/messages", name="api_forum/messages_post_collection", methods={"POST"})
     */
    public function create(
        Topic $topic,
        Request $request,
        ValidatorInterface $validator,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher
    ): JsonResponse {
        $this->denyAccessUnlessGranted(ForumVoter::CREATE_MESSAGE, $topic);
        $data = json_decode((string) $request->getContent(), true);
        $message = (new Message())
            ->setCreatedAt(new \DateTime())
            ->setUpdatedAt(new \DateTime())
            ->setTopic($topic)
            ->setContent($data['content'] ?? null)
            ->setAuthor($this->getUser());
        $topic->setUpdatedAt(new \DateTime());
        $validator->validate($message, ['groups' => ['create']]);
        $em->persist($message);
        $em->flush();
        $dispatcher->dispatch(new MessageCreatedEvent($message));

        return new JsonResponse([
            'id' => $message->getId(),
            'html' => $this->renderView('forum/_message.html.twig', ['message' => $message]),
        ], Response::HTTP_CREATED);
    }
}
