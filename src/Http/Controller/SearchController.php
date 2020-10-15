<?php

namespace App\Http\Controller;

use App\Domain\Course\Repository\TechnologyRepository;
use App\Infrastructure\Search\SearchInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SearchController extends AbstractController
{
    /**
     * @Route("/recherche", name="search")
     */
    public function search(
        Request $request,
        SearchInterface $search,
        NormalizerInterface $normalizer,
        TechnologyRepository $technologyRepository
    ): Response {
        $q = $request->query->get('q') ?: '';

        if (!empty($q)) {
            $technology = $technologyRepository->findByName($q);

            if (null !== $technology) {
                /** @var array{'path': string, "params": string[]} $path */
                $path = $normalizer->normalize($technology, 'path');

                return $this->redirectToRoute(
                    $path['path'],
                    $path['params']
                );
            }
        }

        $results = $search->search($q, []);

        return $this->render('pages/search.html.twig', [
            'q' => $q,
            'total' => $results->getTotal(),
            'results' => $results->getItems(),
        ]);
    }
}
