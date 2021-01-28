<?php

namespace App\Http\Security;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Twig\Environment;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private Environment $twig;

    public function __construct(UrlGeneratorInterface $urlGenerator, Environment $twig)
    {
        $this->urlGenerator = $urlGenerator;
        $this->twig = $twig;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): Response
    {
        $attributes = $accessDeniedException->getAttributes();
        if (count($attributes) > 0) {
            $attribute = $attributes[0];
            if (in_array($attribute, [
                CourseVoter::DOWNLOAD_VIDEO,
                CourseVoter::DOWNLOAD_SOURCE,
            ])) {
                $session = $request->getSession();
                if ($session instanceof Session) {
                    $session->getFlashBag()->add('error', 'Vous devez être premium pour pouvoir télécharger les sources ou les vidéos');
                }

                return new RedirectResponse($this->urlGenerator->generate('premium'));
            }
        }

        if (in_array('application/json', $request->getAcceptableContentTypes())) {
            return new JsonResponse(null, Response::HTTP_FORBIDDEN);
        }

        return new Response($this->twig->render('bundles/TwigBundle/Exception/error403.html.twig'), Response::HTTP_FORBIDDEN);
    }
}
