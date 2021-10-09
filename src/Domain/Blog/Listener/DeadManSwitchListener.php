<?php

namespace App\Domain\Blog\Listener;

use App\Domain\Blog\Post;
use App\Domain\Blog\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Listener permettant de pousser la date de publication de l'article post-mortem.
 */
class DeadManSwitchListener implements EventSubscriberInterface
{
    private AuthorizationCheckerInterface $auth;
    private PostRepository $postRepository;
    private EntityManagerInterface $em;

    public function __construct(
        AuthorizationCheckerInterface $auth,
        PostRepository $postRepository,
        EntityManagerInterface $em
    ) {
        $this->auth = $auth;
        $this->postRepository = $postRepository;
        $this->em = $em;
    }

    public static function getSubscribedEvents()
    {
        return [
            AuthenticationEvents::AUTHENTICATION_SUCCESS => 'onAdminDeath',
        ];
    }

    public function onAdminDeath(): void
    {
        if (!$this->auth->isGranted('die')) {
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
