<?php

namespace App\Http\Controller;

use App\Domain\Glossary\Repository\GlossaryItemRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GlossaryController extends AbstractController
{

    #[Route('/lexique', name: 'glossary', methods: ['GET'])]
    public function index(GlossaryItemRepository $glossaryItemRepository): Response
    {
        $letters = range('A', 'Z');
        $wordsByLetters = $glossaryItemRepository->findWordsByLetters();
        return $this->render('glossary/index.html.twig', [
            'letters' => $letters,
            'wordsByLetters' => $wordsByLetters
        ]);
    }
}
