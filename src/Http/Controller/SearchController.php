<?php

namespace App\Http\Controller;

use App\Domain\Course\Repository\TechnologyRepository;
use App\Infrastructure\Search\SearchInterface;
use Knp\Component\Pager\Event\Subscriber\Paginate\Callback\CallbackPagination;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SearchController extends AbstractController
{
    private PaginatorInterface $paginator;

    public function __construct(PaginatorInterface $paginator)
    {
        $this->paginator = $paginator;
    }

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

        $page = (int) $request->get('page', 1) ?: 1;
        $results = $search->search($q, [], 10, $page);
        $paginableResults = new CallbackPagination(fn () => $results->getTotal(), fn () => $results->getItems());

        return $this->render('pages/search.html.twig', [
            'q' => $q,
            'total' => $results->getTotal(),
            'results' => $this->paginator->paginate($paginableResults, $page),
        ]);
    }
}
