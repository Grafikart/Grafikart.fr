<?php

namespace App\Http\Api\Controller;

use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Repository\TechnologyRepository;
use App\Http\Controller\AbstractController;
use App\Infrastructure\Search\SearchInterface;
use App\Infrastructure\Search\SearchResultItemInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SearchController extends AbstractController
{
    private SearchInterface $search;
    private TechnologyRepository $technologyRepository;
    private SerializerInterface $serializer;

    public function __construct(SearchInterface $search, TechnologyRepository $technologyRepository, SerializerInterface $serializer)
    {
        $this->search = $search;
        $this->technologyRepository = $technologyRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/search", name="search")
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->get('q', ''));
        if (empty($q)) {
            return $this->json([]);
        }

        // On trouve les technologies qui correspondent à la recherche
        $technologies = $this->technologyRepository->searchByName($q);
        $technologiesMatches = array_map(fn (Technology $technology) => [
            'title' => $technology->getName(),
            'url' => $this->serializer->serialize($technology, 'path'),
            'category' => 'Technologie',
        ], $technologies);

        // On trouve les contenus qui correspondent à la recheche
        $results = $this->search->search($q, [], 5);
        $contentMatches = array_map(fn (SearchResultItemInterface $item) => [
            'title' => $item->getTitle(),
            'url' => $item->getUrl(),
            'category' => $item->getType(),
        ], $results->getItems());

        return $this->json([
            'items' => array_merge($technologiesMatches, $contentMatches),
            'hits' => $results->getTotal(),
        ]);
    }
}
