<?php

namespace App\Http\Api\Controller;

use App\Domain\Badge\BadgeService;
use App\Domain\Badge\Entity\Badge;
use App\Http\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BadgeController extends AbstractController
{
    private BadgeService $service;

    public function __construct(BadgeService $service)
    {
        $this->service = $service;
    }

    /**
     * @Route("/badges/{badgeName}/unlock", name="badge_unlock", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
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
