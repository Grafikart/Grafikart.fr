<?php

namespace App\Http\Api\Controller;

use App\Domain\Auth\AuthService;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class DarkModeController extends AbstractController
{
    /**
     * @Route("/dark", name="api_dark", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function toggle(AuthService $auth, EntityManagerInterface $em): JsonResponse
    {
        $user = $auth->getUser();
        $user->setDarkMode(!$user->getDarkMode());
        $em->flush();

        return new JsonResponse();
    }
}
