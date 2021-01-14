<?php

namespace App\Http\Controller\Account;

use App\Domain\Auth\UserRepository;
use App\Domain\Profile\Entity\EmailVerification;
use App\Domain\Profile\ProfileService;
use App\Http\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailChangeController extends AbstractController
{
    /**
     * @Route("/email-confirm/{token}", name="user_email_confirm")
     */
    public function confirm(
        EmailVerification $emailVerification,
        ProfileService $service,
        EntityManagerInterface $em,
        UserRepository $userRepository
    ): Response {
        if ($emailVerification->isExpired()) {
            $this->addFlash('error', 'Cette demande de confirmation a expiré');
        } else {
            $user = $userRepository->findOneByEmail($emailVerification->getEmail());

            // Un utilisateur existe déjà avec cet email
            if ($user) {
                $this->addFlash('error', 'Cet email est déjà utilisé');

                return $this->redirectToRoute('auth_login');
            }

            $service->updateEmail($emailVerification);
            $em->flush();
            $this->addFlash('success', 'Votre email a bien été modifié');
        }

        return $this->redirectToRoute('user_edit');
    }
}
