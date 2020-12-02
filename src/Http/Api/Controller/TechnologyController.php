<?php

namespace App\Http\Api\Controller;

use App\Domain\Course\Entity\Technology;
use App\Domain\Course\Repository\TechnologyRepository;
use App\Http\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TechnologyController extends AbstractController
{
    /**
     * @Route("/technologies/search", name="technology_search")
     */
    public function search(Request $request, TechnologyRepository $technologyRepository): JsonResponse
    {
        $search = $request->query->get('q');
        if (null === $search) {
            return $this->json([]);
        }
        $technologies = $technologyRepository->searchByName($search);

        return $this->json(array_map(fn (Technology $t) => [
            'name' => $t->getName(),
            'slug' => $t->getSlug(),
        ], $technologies));
    }
}
