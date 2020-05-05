<?php

namespace App\Http\Admin\Controller;

use App\Core\Helper\Paginator\PaginatorInterface;
use App\Domain\Application\Entity\Content;
use App\Http\Admin\Data\CrudDataInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * @template E
 * @method \App\Domain\Auth\User getUser()
 */
abstract class CrudController extends BaseController
{

    /**
     * @var class-string<E> $entity
     */
    protected string $entity = Content::class;
    protected string $templatePath = 'blog';
    protected string $menuItem = '';
    protected string $routePrefix = '';
    protected string $searchField = 'title';
    protected array $events = [
        'update' => null,
        'delete' => null,
        'create' => null
    ];
    protected EntityManagerInterface $em;
    protected PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;
    private RequestStack $requestStack;

    public function __construct(
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher,
        RequestStack $requestStack
    ) {
        $this->em = $em;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
        $this->requestStack = $requestStack;
    }

    public function crudIndex(QueryBuilder $query = null): Response
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $query = $query ?: $this->getRepository()
            ->createQueryBuilder('row')
            ->orderBy('row.createdAt', 'DESC');
        if ($request->get('q')) {
            $query = $this->applySearch($request->get('q'), $query);
        }
        $this->paginator->allowSort('row.id', 'row.title');
        $rows = $this->paginator->paginate($query->getQuery());
        return $this->render("admin/{$this->templatePath}/index.html.twig", [
            'rows'   => $rows,
            'menu'   => $this->menuItem,
            'prefix' => $this->routePrefix
        ]);

    }

    public function crudEdit(CrudDataInterface $data): Response
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $form = $this->createForm($data->getFormClass(), $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var E $entity */
            $entity = $data->getEntity();
            $data->hydrate();
            $this->em->flush();
            if ($this->events['update'] ?? null) {
                $this->dispatcher->dispatch(new $this->events['update']($entity));
            }
            $this->addFlash('success', 'Le contenu a bien été modifié');
            return $this->redirectToRoute($this->routePrefix . '_edit', ['id' => $entity->getId()]);
        }

        return $this->render("admin/{$this->templatePath}/edit.html.twig", [
            'form'   => $form->createView(),
            'entity' => $data->getEntity(),
            'menu'   => $this->menuItem
        ]);
    }

    public function crudNew(CrudDataInterface $data): Response
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $form = $this->createForm($data->getFormClass(), $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var E $entity */
            $entity = $data->getEntity();
            $data->hydrate();
            $this->em->persist($entity);
            $this->em->flush();
            if ($this->events['create'] ?? null) {
                $this->dispatcher->dispatch(new $this->events['create']($data->getEntity()));
            }
            $this->addFlash('success', 'Le contenu a bien été créé');
            return $this->redirectToRoute($this->routePrefix . '_edit', ['id' => $entity->getId()]);
        }

        return $this->render("admin/{$this->templatePath}/new.html.twig", [
            'form'   => $form->createView(),
            'entity' => $data->getEntity(),
            'menu'   => $this->menuItem
        ]);
    }

    public function crudDelete(object $entity, ?string $redirectRoute = null): RedirectResponse
    {
        $this->em->remove($entity);
        if ($this->events['delete'] ?? null) {
            $this->dispatcher->dispatch(new $this->events['delete']($entity));
        }
        $this->em->flush();
        $this->addFlash('success', 'Le contenu a bien été supprimé');
        return $this->redirectToRoute($redirectRoute ?: ($this->routePrefix . '_index'));
    }

    public function getRepository(): EntityRepository
    {
        return $this->em->getRepository($this->entity);
    }

    protected function applySearch(string $search, QueryBuilder $query): QueryBuilder
    {
        return $query->where("LOWER(row.{$this->searchField}) LIKE :search")
            ->setParameter('search', "%" . strtolower($search) . "%");
    }

}
