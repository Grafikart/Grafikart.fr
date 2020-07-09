<?php

namespace App\Http\Controller;

use App\Infrastructure\Search\SearchInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request, SearchInterface $search): Response
    {
        $q = $request->query->get('q');

        return $this->render('pages/search.html.twig', [
            'q' => $q,
            'results' => $search->search($q, [])['hits'],
        ]);
    }
}
