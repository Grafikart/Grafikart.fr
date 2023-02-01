<?php

namespace App\Http\Controller;

use App\Domain\Badge\BadgeService;
use App\Domain\Badge\Entity\Badge;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class BadgeController extends AbstractController
{
    #[Route(path: '/badge/unlock/{badge_action}', name: 'badge_unlock')]
    #[IsGranted('ROLE_USER')]
    #[ParamConverter('badge', options: ['mapping' => ['badge_action' => 'action']])]
    public function unlock(Badge $badge, BadgeService $service): RedirectResponse
    {
        if (!$badge->isUnlockable()) {
            throw new NotFoundHttpException();
        }
        $unlocks = $service->unlock($this->getUserOrThrow(), $badge->getAction());
        if (null === $unlocks || empty($unlocks)) {
            $this->addFlash('error', 'Vous avez déjà ce badge');
        }

        $this->addFlash('success', 'Vous avez déjà ce badge');

        return $this->redirectToRoute('user_badges');
    }
}
