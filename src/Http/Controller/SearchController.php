<?php

namespace App\Http\Controller;

use App\Infrastructure\Search\SearchInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Domain\Course\Entity\Technology;
use App\Http\Normalizer\TechnologyPathNormalizer;


class SearchController extends AbstractController
{
    /**
     * @Route("/search", name="search")
     */
    public function search(
      Request $request,
      SearchInterface $search,
      TechnologyPathNormalizer $technologyPathNormalizer
    ): Response {
        $q = $request->query->get('q');

        // Si q n'est pas vide
        if ( !empty( $q ) ) {

          // on commence par chercher une techno correspondante
          $technology = $this->getDoctrine()
              ->getRepository(Technology::class)
              ->findOneBy( ['name' => $q] );

          // si ça matche on fait la redirection vers la techno en normalisant le path
          if ( !empty( $technology ) ) {
            return $this->redirectToRoute(
              $technologyPathNormalizer->normalize( $technology )['path'],
              $technologyPathNormalizer->normalize( $technology )['params']
            );
          }

          // si ça ne matche pas on fait la recherche typesense
          else {
            return $this->render('pages/search.html.twig', [
                'q' => $q,
                'results' => $search->search($q, [])['hits'],
            ]);
          }
        }

        // et enfin si q est vide on redirige vers une page d'erreur
        else {
          return $this->redirectToRoute( 'path_page_derreur' );
        }
    }
}
