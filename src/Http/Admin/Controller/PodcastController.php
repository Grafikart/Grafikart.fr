<?php

namespace App\Http\Admin\Controller;

use App\Domain\Podcast\Entity\Podcast;
use App\Http\Admin\Data\PodcastCrudData;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/podcast', name: 'podcast_')]
class PodcastController extends CrudController
{
    protected string $templatePath = 'podcast';
    protected string $menuItem = 'podcast';
    protected string $entity = Podcast::class;
    protected string $routePrefix = 'admin_podcast';
    protected string $searchField = 'title';

    #[Route(path: '/', name: 'index')]
    public function index(Request $request): Response
    {
        $query = $this->getRepository()->createQueryBuilder('row');
        $state = $request->get('state', 'published');
        if ($request->get('q')) {
            $query = $this->applySearch(trim((string) $request->get('q')), $query);
        } elseif ('published' === $state) {
            $query = $query->where('row.scheduledAt < NOW()')->andWhere('row.scheduledAt IS NOT NULL');
        } elseif ('suggested' === $state) {
            $query = $query->where('row.scheduledAt IS NULL');
        } elseif ('confirmed' === $state) {
            $query = $query->where('row.scheduledAt IS NOT NULL');
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

    #[Route(path: '/new', name: 'new')]
    public function new(): Response
    {
        $podcast = new Podcast();
        $podcast->setAuthor($this->getUserOrThrow());
        $data = new PodcastCrudData($podcast);

        return $this->crudNew($data);
    }

    #[Route(path: '/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(Podcast $podcast): Response
    {
        return $this->crudDelete($podcast);
    }

    #[Route(path: '/{id<\d+>}', name: 'edit')]
    public function edit(Podcast $podcast): Response
    {
        $data = new PodcastCrudData($podcast);

        return $this->crudEdit($data);
    }
}
