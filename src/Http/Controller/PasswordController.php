<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Password\Data\PasswordResetConfirmData;
use App\Domain\Password\Data\PasswordResetRequestData;
use App\Domain\Password\Entity\PasswordResetToken;
use App\Domain\Password\Form\PasswordResetConfirmForm;
use App\Domain\Password\Form\PasswordResetRequestForm;
use App\Domain\Password\PasswordService;
use App\Http\Requirements;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class PasswordController extends AbstractController
{
    #[Route(path: '/password/new', name: 'auth_password_reset')]
    public function reset(Request $request, PasswordService $resetService): Response
    {
        $error = null;
        $data = new PasswordResetRequestData();
        $form = $this->createForm(PasswordResetRequestForm::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $resetService->resetPassword($form->getData());
                $this->addFlash('success', 'Les instructions pour réinitialiser votre mot de passe vous ont été envoyées');

                return $this->redirectToRoute('auth_login');
            } catch (AuthenticationException $e) {
                $error = $e;
            }
        }

        return $this->render('auth/password_reset.html.twig', [
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/password/new/{id:user}/{token:token}', name: 'auth_password_reset_confirm', requirements: ['id' => Requirements::ID])]
    public function confirm(
        Request $request,
        User $user,
        ?PasswordResetToken $token,
        PasswordService $service,
    ): Response {
        if (!$token || $service->isExpired($token) || $token->getUser() !== $user) {
            $this->addFlash('error', 'Ce token a expiré');

            return $this->redirectToRoute('auth_login');
        }
        $error = null;
        $data = new PasswordResetConfirmData();
        $form = $this->createForm(PasswordResetConfirmForm::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->updatePassword($data->getPassword(), $token);
            $this->addFlash('success', 'Votre mot de passe a bien été réinitialisé');

            return $this->redirectToRoute('auth_login');
        }

        return $this->render('auth/password_reset_confirm.html.twig', [
            'error' => $error,
            'form' => $form->createView(),
        ]);
    }
}
