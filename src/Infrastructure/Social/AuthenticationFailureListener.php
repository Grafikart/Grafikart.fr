<?php

namespace App\Infrastructure\Social;

use App\Infrastructure\Social\Exception\UserAuthenticatedException;
use App\Infrastructure\Social\Exception\UserOauthNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Event\AuthenticationFailureEvent;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class AuthenticationFailureListener implements EventSubscriberInterface
{
    private NormalizerInterface $normalizer;
    private EntityManagerInterface $em;
    private SessionInterface $session;

    public function __construct(NormalizerInterface $normalizer, SessionInterface $session, EntityManagerInterface $em)
    {
        $this->normalizer = $normalizer;
        $this->em = $em;
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return [
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onAuthenticationFailure',
        ];
    }

    public function onAuthenticationFailure(AuthenticationFailureEvent $event): void
    {
        $exception = $event->getAuthenticationException();
        if ($exception instanceof UserOauthNotFoundException) {
            $this->onUserNotFound($exception);
        }
        if ($exception instanceof UserAuthenticatedException) {
            $this->onUserAlreadyAuthenticated($exception);
        }
    }

    public function onUserNotFound(UserOauthNotFoundException $exception): void
    {
        $data = $this->normalizer->normalize($exception->getResourceOwner());
        $this->session->set(SocialLoginService::SESSION_KEY, $data);
    }

    public function onUserAlreadyAuthenticated(UserAuthenticatedException $exception): void
    {
        $resourceOwner = $exception->getResourceOwner();
        $user = $exception->getUser();
        /** @var array{type: string} $data */
        $data = $this->normalizer->normalize($exception->getResourceOwner());
        $setter = 'set'.ucfirst($data['type']).'Id';
        $user->$setter($resourceOwner->getId());
        $this->em->flush();
        if ($this->session instanceof Session) {
            $this->session->getFlashBag()->set('success', 'Votre compte a bien été associé à '.$data['type']);
        }
    }
}
