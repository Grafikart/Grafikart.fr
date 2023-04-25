<?php

namespace App\Http\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PagesController extends AbstractController
{
    #[Route(path: '/a-propos', name: 'env')]
    public function env(): Response
    {
        return $this->render('pages/env.html.twig');
    }

    #[Route(path: '/politique-de-confidentialite', name: 'confidentialite')]
    public function confidentialite(): Response
    {
        return $this->render('pages/confidentialite.html.twig');
    }

    #[Route(path: '/ui', name: 'ui')]
    public function ui(): Response
    {
        /** @var string $projectDir */
        $projectDir = $this->getParameter('kernel.project_dir');
        $spritePath = sprintf(
            '%1$s%2$spublic%2$ssprite.svg',
            $projectDir,
            DIRECTORY_SEPARATOR
        );
        $spriteCode = (string)file_get_contents($spritePath);
        preg_match_all('/id="([^"]*)"/i', $spriteCode, $matches);
        return $this->render('pages/ui.html.twig', [
            'icons' => $matches[1]
        ]);
    }

    #[Route(path: '/tchat', name: 'tchat')]
    public function tchat(): RedirectResponse
    {
        return new RedirectResponse('https://discordapp.com/invite/rAuuD7Q');
    }
}
