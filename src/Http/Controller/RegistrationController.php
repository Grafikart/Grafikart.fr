<?php

namespace App\Http\Controller;

use App\Domain\Auth\Authenticator;
use App\Domain\Auth\Event\UserCreatedEvent;
use App\Domain\Auth\User;
use App\Http\Form\RegistrationFormType;
use App\Infrastructure\Security\TokenGeneratorService;
use App\Infrastructure\Social\SocialLoginService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

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
        EventDispatcherInterface $dispatcher,
        SocialLoginService $socialLoginService,
        GuardAuthenticatorHandler $guardAuthenticatorHandler,
        Authenticator $authenticator
    ): Response {
        // Si l'utilisateur est connecté, on le redirige vers la home
        $loggedInUser = $this->getUser();
        if ($loggedInUser) {
            return $this->redirectToRoute('user_profil');
        }

        $user = new User();
        $rootErrors = [];
        // Si l'utilisateur provient de l'oauth, on préremplit ses données
        $isOauthUser = $request->get('oauth') ? $socialLoginService->hydrate($user) : false;
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $form->has('plainPassword') ? $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                ) : ''
            );
            $user->setCreatedAt(new \DateTime());
            $user->setConfirmationToken($isOauthUser ? null : $tokenGenerator->generate(60));
            $user->setNotificationsReadAt(new \DateTimeImmutable());
            $em->persist($user);
            $em->flush();
            $dispatcher->dispatch(new UserCreatedEvent($user, $isOauthUser));

            if ($isOauthUser) {
                $this->addFlash(
                    'success',
                    'Votre compte a été créé avec succès'
                );

                return $guardAuthenticatorHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main'
                ) ?: $this->redirectToRoute('user_edit');
            }

            $this->addFlash(
                'success',
                'Un message avec un lien de confirmation vous a été envoyé par mail. Veuillez suivre ce lien pour activer votre compte.'
            );

            return $this->redirectToRoute('auth_login');
        } elseif ($form->isSubmitted()) {
            /** @var FormError $error */
            foreach ($form->getErrors() as $error) {
                if (null === $error->getCause()) {
                    $rootErrors[] = $error;
                }
            }
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView(),
            'errors' => $rootErrors,
            'menu' => 'register',
            'oauth_registration' => $request->get('oauth'),
            'oauth_type' => $socialLoginService->getOauthType(),
        ]);
    }

    /**
     * @Route("/inscription/confirmation/{id<\d+>}", name="register_confirm")
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
