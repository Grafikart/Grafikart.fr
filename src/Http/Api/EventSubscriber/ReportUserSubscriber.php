<?php

namespace App\Http\Api\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Domain\Auth\User;
use App\Domain\Forum\Entity\Report;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Security;

class ReportUserSubscriber implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['setUser', EventPriorities::POST_DESERIALIZE],
        ];
    }

    public function setUser(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $report = $request->get('data');
        $method = $request->getMethod();
        $user = $this->security->getUser();

        if (
            !$report instanceof Report ||
            Request::METHOD_POST !== $method ||
            !$user instanceof User
        ) {
            return;
        }

        $request->attributes->set(
            'data',
            $report->setAuthor($user)->setCreatedAt(new \DateTime())
        );
    }
}
