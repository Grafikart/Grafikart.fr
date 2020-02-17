<?php

namespace App\Infrastructure\Admin\Controller;

use App\Infrastructure\Admin\Data\CrudDataInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

abstract class CrudController extends BaseController
{

    protected string $templatePath = 'blog';
    protected string $entity = '';
    protected string $menuItem = '';
    protected array $events = [
        'update' => '',
        'delete' => '',
        'create' => ''
    ];
    protected EntityManagerInterface $em;
    private PaginatorInterface $paginator;
    private EventDispatcherInterface $dispatcher;
    private RequestStack $requestStack;

    public function __construct(
        EntityManagerInterface $em,
        PaginatorInterface $paginator,
        EventDispatcherInterface $dispatcher,
        RequestStack $requestStack
    )
    {
        $this->em = $em;
        $this->paginator = $paginator;
        $this->dispatcher = $dispatcher;
        $this->requestStack = $requestStack;
    }

    public function crudIndex(): Response
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $query = $this->getRepository()
            ->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC');
        if ($request->get('q')) {
            $query = $query->where('p.title LIKE :title')
                ->setParameter('title', "%" . $request->get('q') . "%");
        }
        $page = $request->query->getInt('page', 1);
        $rows = $this->paginator->paginate(
            $query->getQuery(),
            $page,
            10
        );
        return $this->render("admin/{$this->templatePath}/index.html.twig", [
            'rows' => $rows,
            'page' => $page,
            'menu' => $this->menuItem
        ]);

    }

    public function crudEdit(CrudDataInterface $data): Response
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();
        $form = $this->createForm($data->getFormClass(), $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data->hydrate($data->getEntity(), $this->em);
            $this->em->flush();
            $this->dispatcher->dispatch(new $this->events['update']($data->getEntity()));
            $this->addFlash('success', 'Le contenu a bien été modifié');
        }

        return $this->render('admin/blog/edit.html.twig', [
            'form' => $form->createView(),
            'entity' => $data->getEntity(),
            'menu' => 'blog'
        ]);
    }

    public function getRepository(): EntityRepository
    {
        /** @var EntityRepository $repository */
        $repository = $this->em->getRepository($this->entity);
        return $repository;
    }

}
