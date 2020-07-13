<?php

namespace App\Http\Controller;

use App\Domain\Notification\NotificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NotificationsController extends AbstractController
{
    /**
     * @Route("/notifications", name="notifications")
     * @IsGranted("ROLE_USER")
     */
    public function index(NotificationService $service): Response
    {
        $notifications = $service->forUser($this->getUser());

        return $this->render('notifications/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }
}
