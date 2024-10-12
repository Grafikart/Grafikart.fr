<?php

namespace App\Http\Api\Controller\Forum;

use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Event\MessageCreatedEvent;
use App\Domain\Forum\Event\PreMessageCreatedEvent;
use App\Domain\Forum\TopicService;
use App\Http\Api\Controller\AbstractApiController;
use App\Http\Requirements;
use App\Http\Security\ForumVoter;
use App\Http\ValueResolver\Attribute\MapHydratedEntity;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method \App\Domain\Auth\User getUser()
 */
#[Route(path: '/forum', name: 'forum_')]
class ForumMessageController extends AbstractApiController
{
    #[Route('/messages/{message}', name: 'message', requirements: ['message' => Requirements::ID], methods: ['GET'])]
    public function show(
        Message $message,
    ): JsonResponse {
        return $this->json($message, context: ['groups' => ['read:message']]);
    }

    #[Route('/messages/{message}', requirements: ['message' => Requirements::ID], methods: ['PUT'])]
    #[IsGranted(ForumVoter::UPDATE_MESSAGE, subject: 'message')]
    public function update(
        #[MapHydratedEntity(groups: ['update:message'])]
        Message $message,
        EntityManagerInterface $em,
    ): JsonResponse {
        $em->flush();

        return $this->json($message, context: ['groups' => ['read:message']]);
    }

    #[Route('/messages/{message}', requirements: ['message' => Requirements::ID], methods: ['DELETE'])]
    #[IsGranted(ForumVoter::DELETE_MESSAGE, subject: 'message')]
    public function delete(
        Message $message,
        EntityManagerInterface $em,
    ): JsonResponse {
        $em->remove($message);
        $em->flush();

        return new JsonResponse(null, 204);
    }

    #[Route('/topics/{topic}/messages', name: 'messages', requirements: ['topic' => Requirements::ID], methods: ['POST'])]
    #[IsGranted(ForumVoter::CREATE_MESSAGE, subject: 'topic')]
    public function create(
        Topic $topic,
        Request $request,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
    ): JsonResponse {
        $data = json_decode((string) $request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $message = (new Message())
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(new \DateTimeImmutable())
            ->setTopic($topic)
            ->setNotification((bool) ($data['notification'] ?? false))
            ->setContent($data['content'] ?? null)
            ->setAuthor($this->getUser());
        $this->validateOrThrow($message);
        $dispatcher->dispatch(new PreMessageCreatedEvent($message));
        $em->persist($message);
        $em->flush();
        $dispatcher->dispatch(new MessageCreatedEvent($message));

        return new JsonResponse([
            'id' => $message->getId(),
            'html' => $this->renderView('forum/_message.html.twig', ['message' => $message]),
        ], Response::HTTP_CREATED);
    }

    #[Route('/messages/{message}/solve', name: 'message_solve ', requirements: ['id' => Requirements::ID], methods: ['POST'])]
    #[IsGranted(ForumVoter::SOLVE_MESSAGE, subject: 'message')]
    public function solve(Message $message, TopicService $service): JsonResponse
    {
        $service->messageSolveTopic($message);

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
