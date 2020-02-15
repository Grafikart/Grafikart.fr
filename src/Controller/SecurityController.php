<?php

namespace App\Controller;

use App\Domain\Auth\Data\PasswordResetConfirmData;
use App\Domain\Auth\Data\PasswordResetRequestData;
use App\Domain\Auth\Entity\PasswordResetToken;
use App\Domain\Auth\Form\PasswordResetConfirmForm;
use App\Domain\Auth\Form\PasswordResetRequestForm;
use App\Domain\Auth\Service\PasswordResetService;
use App\Domain\Auth\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="auth_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/password/new", name="auth_password_reset")
     */
    public function passwordReset(Request $request, PasswordResetService $resetService): Response
    {
        $error = null;
        $data = new PasswordResetRequestData();
        $form = $this->createForm(PasswordResetRequestForm::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $resetService->resetPassword($form->getData());
                $this->addFlash('success', 'Les instructions pour réinitialiser votre mot de passes vous ont été envoyées');
                return $this->redirectToRoute('auth_login');
            } catch (\Exception $e) {
                $error = $e;
            }
        }
        return $this->render('auth/password_reset.html.twig', [
            'error' => $error,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/password/new/{id}/{token}", name="auth_password_reset_confirm")
     */
    public function passwordResetConfirm(Request $request, User $user, PasswordResetToken $token, PasswordResetService $service): Response
    {
        if ($service->isExpired($token) || $token->getUser() !== $user) {
            $this->addFlash('error', 'Ce token a expiré');
            return $this->redirectToRoute('auth_login');
        }
        $error = null;
        $data = new PasswordResetConfirmData();
        $form = $this->createForm(PasswordResetConfirmForm::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->updatePassword($data->getPassword(), $user);
            $this->addFlash('success', 'Votre mot de passe a bien été réinitialisé');
            return $this->redirectToRoute('auth_login');
        }
        return $this->render('auth/password_reset_confirm.html.twig', [
            'error' => $error,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/logout", name="auth_logout")
     */
    public function logout(): void
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

}
