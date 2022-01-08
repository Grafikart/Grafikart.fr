<?php

namespace App\Domain\Blog\Listener;

use App\Domain\Auth\User;
use App\Domain\Blog\Post;
use App\Domain\Blog\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

/**
 * Listener permettant de pousser la date de publication de l'article post-mortem.
 */
class DeadManSwitchListener implements EventSubscriberInterface
{
    public function __construct(
        private readonly AccessDecisionManagerInterface $permission,
        private readonly PostRepository $postRepository,
        private readonly EntityManagerInterface $em
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAdminDeath',
        ];
    }

    public function onAdminDeath(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!($user instanceof User)) {
            return;
        }
        if (!$this->permission->decide($event->getAuthenticationToken(), ['die'])) {
            return;
        }
        $post = $this->postRepository->findOneBy(['slug' => 'dead-or-bugged']);
        if (!($post instanceof Post)) {
            return;
        }
        $post->setCreatedAt(new \DateTime('+7 days'));
        $this->em->flush();
    }
}
