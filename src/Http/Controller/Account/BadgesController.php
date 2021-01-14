<?php

namespace App\Http\Controller\Account;

use App\Domain\Badge\BadgeService;
use App\Http\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BadgesController extends AbstractController
{
    private BadgeService $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /**
     * @Route("/profil/badges", name="user_badges")
     * @IsGranted("ROLE_USER")
     */
    public function index(): Response
    {
        $badges = $this->badgeService->getBadges();
        $unlocks = $this->badgeService->getUnlocksForUser($this->getUserOrThrow());

        return $this->render('account/badges.html.twig', [
            'badges' => $badges,
            'unlocks' => $unlocks,
        ]);
    }
}
