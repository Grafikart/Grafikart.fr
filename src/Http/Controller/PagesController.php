<?php

namespace App\Http\Controller;

use App\Http\Form\SchoolImportForm;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PagesController extends AbstractController
{
    #[Route(path: '/a-propos', name: 'env')]
    public function env(): Response
    {
        return $this->render('pages/env.html.twig');
    }

    #[Route(path: '/premium/ecoles', name: 'about_schools')]
    public function ecoles(): Response
    {
        // Fausse donnée pour la présentation du système de progression
        $formations = [
            [
                'title' => 'Apprendre React',
                'count' => 31,
                'progress' => 50,
                'icon' => 'react'
            ],
            [
                'title' => "Comprendre Git",
                'count' => 18,
                'progress' => 20,
                'icon' => 'git'
            ],
            [
                'title' => 'Apprendre JavaScript',
                'count' => 56,
                'progress' => 100,
                'icon' => 'javascript'
            ],
            [
                'title' => "L'algorithmique",
                'count' => 10,
                'progress' => 100,
                'icon' => 'algorithmique'
            ],
            [
                'title' => "Découverte du CSS",
                'count' => 37,
                'progress' => 90,
                'icon' => 'css'
            ],
            [
                'title' => "Comprendre l'HTML",
                'count' => 10,
                'progress' => 100,
                'icon' => 'html'
            ]
        ];
        return $this->render('pages/premium_school.html.twig', [
            'formations' => $formations,
            'form' => $this->createForm(SchoolImportForm::class),
        ]);
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
            'icons' => $matches[1],
        ]);
    }

    #[Route(path: '/tchat', name: 'tchat')]
    public function tchat(): RedirectResponse
    {
        return new RedirectResponse('https://discordapp.com/invite/rAuuD7Q');
    }
}
