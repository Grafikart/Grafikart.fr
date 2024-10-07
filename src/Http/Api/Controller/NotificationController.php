<?php

namespace App\Http\Api\Controller;

use App\Domain\Auth\AuthService;
use App\Domain\Notification\NotificationService;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class NotificationController extends AbstractController
{
    #[Route(path: '/notifications/read', name: 'api_notification_read', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function readAll(AuthService $auth, NotificationService $service): JsonResponse
    {
        $user = $auth->getUser();
        $service->readAll($user);

        return new JsonResponse();
    }
}
