<?php

namespace App\Http\Admin\Controller;

use App\Domain\Podcast\Entity\Podcast;
use App\Http\Admin\Data\PodcastCrudData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/podcast", name="podcast_")
 */
class PodcastController extends CrudController
{
    protected string $templatePath = 'podcast';
    protected string $menuItem = 'podcast';
    protected string $entity = Podcast::class;
    protected string $routePrefix = 'admin_podcast';
    protected string $searchField = 'title';

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request): Response
    {
        $query = $this->getRepository()->createQueryBuilder('row');
        $state = $request->get('state', 'published');
        if ($request->get('q')) {
            $query = $this->applySearch(trim($request->get('q')), $query);
        } elseif ('published' === $state) {
            $query = $query->where('row.scheduledAt < NOW()');
        } elseif ('suggested' === $state) {
            $query = $query->where('row.confirmedAt IS NULL');
        } elseif ('confirmed' === $state) {
            $query = $query->where('row.confirmedAt IS NOT NULL');
        }
        $query = $query
            ->addOrderBy('row.createdAt', 'DESC')
            ->addOrderBy('row.scheduledAt', 'DESC')
            ->addOrderBy('row.createdAt', 'DESC');
        $this->paginator->allowSort('row.id', 'row.title');
        $rows = $this->paginator->paginate($query->getQuery());

        return $this->render("admin/{$this->templatePath}/index.html.twig", [
            'rows' => $rows,
            'state' => $state,
            'searchable' => true,
            'menu' => $this->menuItem,
            'prefix' => $this->routePrefix,
        ]);
    }

    /**
     * @Route("/new", name="new")
     */
    public function new(): Response
    {
        $podcast = new Podcast();
        $data = new PodcastCrudData($podcast);

        return $this->crudNew($data);
    }

    /**
     * @Route("/{id<\d+>}", name="delete", methods={"DELETE"})
     */
    public function delete(Podcast $podcast): Response
    {
        return $this->crudDelete($podcast);
    }

    /**
     * @Route("/{id<\d+>}", name="edit")
     */
    public function edit(Podcast $podcast): Response
    {
        $data = new PodcastCrudData($podcast);

        return $this->crudEdit($data);
    }
}
