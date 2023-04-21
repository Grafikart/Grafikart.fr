<?php

namespace App\Http\Api\Controller;

use App\Domain\Badge\BadgeService;
use App\Domain\Badge\Entity\Badge;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BadgeController extends AbstractController
{
    public function __construct(private readonly BadgeService $service)
    {
    }

    #[Route(path: '/badges/{badgeName}/unlock', name: 'badge_unlock', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function unlock(string $badgeName): JsonResponse
    {
        if (!in_array($badgeName, Badge::REQUEST_UNLOCKABLE)) {
            throw new AccessDeniedException('Aucun badge ne correspond à ce nom');
        }

        $unlocks = $this->service->unlock($this->getUserOrThrow(), $badgeName);
        if (null === $unlocks || empty($unlocks)) {
            return new JsonResponse(null, Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json($unlocks[0]->getBadge());
    }
}
