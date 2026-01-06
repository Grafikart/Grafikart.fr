<?php

namespace App\Infrastructure\Inertia;

use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Automatically inject global properties on inertia views.
 */
final readonly class InertiaSubscriber implements EventSubscriberInterface
{
    public function __construct(private InertiaInterface $inertia)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onControllerEvent',
        ];
    }

    public function onControllerEvent(ControllerEvent $event)
    {
        $session = $event->getRequest()->getSession();

        if (!$session instanceof FlashBagAwareSessionInterface) {
            return;
        }

        if ($message = $session->getFlashBag()->get('success')) {
            $this->inertia->share('flash', [
                'type' => 'success',
                'message' => $message,
            ]);
        }

        if ($message = $session->getFlashBag()->get('error')) {
            $this->inertia->share('flash', [
                'type' => 'error',
                'message' => $message,
            ]);
        }
    }
}
