<?php

namespace App\Http\Controller;

use App\Core\Security\TokenGeneratorService;
use App\Domain\Auth\Event\UserCreatedEvent;
use App\Domain\Auth\User;
use App\Http\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/inscription", name="register");
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em,
        TokenGeneratorService $tokenGenerator,
        EventDispatcherInterface $dispatcher
    ): Response {
        $loggedInUser = $this->getUser();
        if ($loggedInUser) {
            return $this->redirectToRoute('user_profil');
        }
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword($user, $form->get('plainPassword')->getData())
            );
            $user->setCreatedAt(new \DateTime());
            $user->setConfirmationToken($tokenGenerator->generate(60));
            $em->persist($user);
            $em->flush();
            $dispatcher->dispatch(new UserCreatedEvent($user));

            $this->addFlash(
                'success',
                'Un message avec un lien de confirmation vous a été envoyé par mail. Veuillez suivre ce lien pour activer votre compte.'
            );

            return $this->redirectToRoute('register');
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/inscription/confirmation/{id}", name="register_confirm")
     */
    public function confirmToken(User $user, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $token = $request->get('token');
        if (empty($token) || $token !== $user->getConfirmationToken()) {
            $this->addFlash('error', "Ce token n'est pas valide");

            return $this->redirectToRoute('register');
        }

        if ($user->getCreatedAt() < new \DateTime('-2 hours')) {
            $this->addFlash('error', 'Ce token a expiré');

            return $this->redirectToRoute('register');
        }

        $user->setConfirmationToken(null);
        $em->flush();
        $this->addFlash('success', 'Votre compte a été validé.');

        return $this->redirectToRoute('auth_login');
    }
}
