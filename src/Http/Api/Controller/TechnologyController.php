<?php

namespace App\Http\Api\Controller;

use App\Component\ObjectMapper\ObjectMapperInterface;
use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Repository\TechnologyRepository;
use App\Http\Data\OptionItemData;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class TechnologyController extends AbstractController
{
    #[Route(path: '/technologies', name: 'technology_search', methods: 'GET')]
    public function search(
        Request $request,
        TechnologyRepository $technologyRepository,
        ObjectMapperInterface $mapper,
    ): JsonResponse {
        $search = $request->query->get('q');
        if (null === $search) {
            return $this->json([]);
        }
        $technologies = $technologyRepository->searchByName($search);

        return $this->json(array_map(fn (Technology $t) => $mapper->map($t, OptionItemData::class), $technologies));
    }
}
