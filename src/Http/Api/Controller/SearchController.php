<?php

namespace App\Http\Api\Controller;

use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Repository\TechnologyRepository;
use App\Http\Controller\AbstractController;
use App\Infrastructure\Search\SearchInterface;
use App\Infrastructure\Search\SearchResultItemInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class SearchController extends AbstractController
{
    public function __construct(
        private readonly SearchInterface $search,
        private readonly TechnologyRepository $technologyRepository,
        private readonly SerializerInterface $serializer
    ) {
    }

    #[Route(path: '/search', name: 'search')]
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));
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
