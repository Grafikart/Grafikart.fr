<?php

namespace App\Http\Security;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Twig\Environment;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private Environment $twig;
    private SessionInterface $session;

    public function __construct(SessionInterface $session, UrlGeneratorInterface $urlGenerator, Environment $twig)
    {
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
        $this->session = $session;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException)
    {
        $attributes = $accessDeniedException->getAttributes();
        if (count($attributes) > 0) {
            $attribute = $attributes[0];
            if (in_array($attribute, [
                CourseVoter::DOWNLOAD_VIDEO,
                CourseVoter::DOWNLOAD_SOURCE,
            ])) {
                $this->session->getBag((new FlashBag())->getName())->set('error', 'Vous devez être premium pour pouvoir télécharger les sources ou les vidéos');

                return new RedirectResponse($this->urlGenerator->generate('premium'));
            }
        }

        return new Response($this->twig->render('bundles/TwigBundle/Exception/error403.html.twig'));
    }
}
