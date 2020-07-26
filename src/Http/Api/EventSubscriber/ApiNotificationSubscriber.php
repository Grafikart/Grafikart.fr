<?php

namespace App\Http\Api\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use App\Domain\Notification\Entity\Notification;
use App\Domain\Notification\Event\NotificationCreatedEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiNotificationSubscriber implements EventSubscriberInterface
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['dispatchNotificationCreation', EventPriorities::PRE_WRITE],
        ];
    }

    public function dispatchNotificationCreation(ViewEvent $event): void
    {
        $controllerResult = $event->getControllerResult();
        $request = $event->getRequest();
        if (
            !$controllerResult instanceof Notification
            || !($attributes = RequestAttributesExtractor::extractAttributes($request))
            || !$attributes['persist']
            || 'POST' !== $request->getMethod()
        ) {
            return;
        }

        $this->dispatcher->dispatch(new NotificationCreatedEvent($controllerResult));
    }
}
