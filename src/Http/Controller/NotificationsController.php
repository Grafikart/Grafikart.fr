<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Notification\NotificationService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @method User getUser()
 */
class NotificationsController extends AbstractController
{
    #[Route(path: '/notifications', name: 'notifications')]
    #[IsGranted('ROLE_USER')]
    public function index(NotificationService $service): Response
    {
        $notifications = $service->forUser($this->getUser(), 15);

        return $this->render('notifications/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }
}
