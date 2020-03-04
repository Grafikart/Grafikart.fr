<?php

namespace App\Http\Admin\Firewall;

use App\Http\Admin\Controller\BaseController;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Limite l'accès à l'administration en vérifiant le rôle de l'utilisateur
 */
class AdminRequestListener implements EventSubscriberInterface
{

    private AuthorizationCheckerInterface $auth;
    private string $adminPrefix;

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onController',
            RequestEvent::class    => 'onRequest'
        ];
    }

    public function __construct(string $adminPrefix, AuthorizationCheckerInterface $auth)
    {
        $this->auth = $auth;
        $this->adminPrefix = $adminPrefix;
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $uri = '/' . trim($event->getRequest()->getRequestUri(), '/') . '/';
        $prefix = '/' . trim($this->adminPrefix, '/') . '/';
        if (
            substr($uri, 0, mb_strlen($prefix)) === $prefix  &&
            !$this->auth->isGranted('CMS_MANAGE')
        ) {
            $exception = new AccessDeniedException();
            $exception->setSubject($event->getRequest());
            throw $exception;
        }
    }

    /**
     * Vérifie que l'utilisateur peux accéder aux controller de l'administration
     *
     * Cette sécurité fait doublon avec l'évènement RequestEvent, mais permet une sécurité supplémentaire dans le cas
     * ou une action d'un controller se retrouve dans une URL qui n'est pas préfixé par le chemin de l'administration
     */
    public function onController(ControllerEvent $event): void
    {
        if ($event->isMasterRequest() === false) {
            return;
        }
        $controller = $event->getController();
        if (is_array($controller) && $controller[0] instanceof BaseController && !$this->auth->isGranted('CMS_MANAGE')) {
            $exception = new AccessDeniedException();
            $exception->setSubject($event->getRequest());
            throw $exception;
        }
    }
}
