<?php

namespace App\Http\Controller;

use App\Domain\Auth\Authenticator;
use App\Domain\Auth\Event\UserBeforeCreatedEvent;
use App\Domain\Auth\Event\UserCreatedEvent;
use App\Domain\Auth\User;
use App\Domain\Coupon\CouponClaimerService;
use App\Domain\Coupon\DTO\CouponClaimDTO;
use App\Http\Form\RegistrationFormType;
use App\Http\Requirements;
use App\Infrastructure\Security\TokenGeneratorService;
use App\Infrastructure\Social\SocialLoginService;
use App\Infrastructure\Spam\GeoIpService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    #[Route(path: '/inscription', name: 'register')]
    public function register(
        Request                     $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface      $em,
        TokenGeneratorService       $tokenGenerator,
        EventDispatcherInterface    $dispatcher,
        SocialLoginService          $socialLoginService,
        UserAuthenticatorInterface  $authenticator,
        Authenticator               $appAuthenticator,
        CouponClaimerService        $couponClaimerService,
        GeoIpService                $ipService,
    ): Response {
        // Si l'utilisateur est connecté, on le redirige vers la home
        $loggedInUser = $this->getUser();
        if ($loggedInUser) {
            return $this->redirectToRoute('user_profil');
        }

        $user = new User();
        $rootErrors = [];
        // Si l'utilisateur provient de l'oauth, on préremplit ses données
        $isOauthUser = $request->get('oauth') ? $socialLoginService->hydrate($request->getSession(), $user) : false;
        $env = $this->getParameter('kernel.environment');
        $form = $this->createForm(RegistrationFormType::class, $user, [
            'with_captcha' => $env !== 'test',
        ]);
        if ($request->query->get('coupon')) {
            $form->get('coupon')->setData($request->get('coupon'));
        }
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On shadow inscrit les bots
            $location = $ipService->getLocation($request->getClientIp() ?? '');
            if ($location && in_array($location->country, ['IN','VN', 'RU', 'CN'])) {
                $this->addFlash(
                    'success',
                    'Votre compte a été créé avec succès'
                );
                return $this->redirectToRoute('auth_login');
            }

            // On enregistre l'utilisateur
            $user
                ->setPassword(
                    $form->has('plainPassword') ? $hasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    ) : ''
                )
                ->setLastLoginIp($request->getClientIp())
                ->setCreatedAt(new \DateTime())
                ->setConfirmationToken($isOauthUser ? null : $tokenGenerator->generate(60))
                ->setNotificationsReadAt(new \DateTimeImmutable())
            ;
            $coupon = $form->get('coupon')->getData();

            if ($coupon) {
                $couponClaimerService->claim(new CouponClaimDTO(user: $user, code: $coupon));
            }

            $dispatcher->dispatch(new UserBeforeCreatedEvent($user, $request));
            $em->persist($user);
            $em->flush();
            $dispatcher->dispatch(new UserCreatedEvent($user, $isOauthUser));

            if ($isOauthUser) {
                $this->addFlash(
                    'success',
                    'Votre compte a été créé avec succès'
                );

                return $authenticator->authenticateUser($user, $appAuthenticator, $request) ?: $this->redirectToRoute('user_edit');
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
            'oauth_type' => $socialLoginService->getOauthType($request->getSession()),
        ]);
    }

    #[Route(path: '/inscription/confirmation/{id}', name: 'register_confirm', requirements: ['id' => Requirements::ID])]
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
