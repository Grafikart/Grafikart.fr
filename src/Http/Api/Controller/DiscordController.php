<?php

namespace App\Http\Api\Controller;

use App\Domain\Auth\UserRepository;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DiscordController extends AbstractController
{
    /**
     * @Route("/discord/premium", name="discord_premium")
     */
    public function premium(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findPremiumDiscordIds();

        return $this->json($users);
    }
}
