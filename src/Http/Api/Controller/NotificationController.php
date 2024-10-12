<?php

namespace App\Http\Api\Controller;

use App\Domain\Auth\AuthService;
use App\Domain\Notification\Entity\Notification;
use App\Domain\Notification\Event\NotificationCreatedEvent;
use App\Domain\Notification\NotificationService;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class NotificationController extends AbstractController
{
    public function __construct(
        private readonly NotificationService $service,
    ) {
    }

    #[Route(path: '/notifications', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): JsonResponse
    {
        $user = $this->getUserOrThrow();
        $notifications = $this->service->forUser($user, 15);

        return $this->json($notifications, context: ['groups' => ['read:notification']]);
    }

    #[Route(path: '/notifications', methods: ['POST'])]
    #[IsGranted('CREATE_NOTIFICATION')]
    public function create(
        #[MapRequestPayload(serializationContext: ['groups' => ['create:notification']])]
        Notification $notification,
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher,
    ): JsonResponse {
        $em->persist($notification);
        $em->flush();
        $dispatcher->dispatch(new NotificationCreatedEvent($notification));

        return $this->json($notification, context: ['groups' => ['read:notification']]);
    }

    #[Route(path: '/notifications/read', name: 'api_notification_read', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function readAll(AuthService $auth, NotificationService $service): JsonResponse
    {
        $user = $auth->getUser();
        $service->readAll($user);

        return new JsonResponse();
    }
}
