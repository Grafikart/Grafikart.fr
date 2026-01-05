<?php

namespace App\Http\Admin\Controller;

use App\Component\ObjectMapper\ObjectMapperInterface;
use App\Domain\Application\Entity\Content;
use App\Helper\Paginator\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Psr\EventDispatcher\EventDispatcherInterface;
use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use function PHPUnit\Framework\assertEquals;

/**
 * @template E
 *
 * @method \App\Domain\Auth\User getUser()
 */
abstract class InertiaController extends BaseController
{
    /**
     * @var class-string<E>
     */
    protected string $entityClass = Content::class;
    protected string $routePrefix = '';
    protected string $itemDataClass = \stdClass::class;
    protected string $formDataClass = \stdClass::class;
    protected string $inputDataClass = \stdClass::class;
    protected string $componentDirectory = '';
    protected array $events = [
        'update' => null,
        'delete' => null,
        'create' => null,
    ];

    public function __construct(
        protected readonly EntityManagerInterface $em,
        protected readonly PaginatorInterface $paginator,
        private readonly InertiaInterface $inertia,
        private readonly NormalizerInterface $normalizer,
        protected readonly ObjectMapperInterface $mapper,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * Rend un composant en utilisant le normalizer pour le second paramètre
     */
    protected function renderComponent(string $view, array $parameters = [], array $context = []): Response
    {
        return $this->inertia->render($view, $this->normalizer->normalize($parameters, 'json', $context));
    }

    /**
     * Redirige en envoyant le bon type de réponse en fonction du contexte
     */
    protected function redirectToInertiaRoute(string $name, array $params = []): Response
    {
        // Pour les requêtes Inertia, redirige classiquement
        if ($this->requestStack->getMainRequest()->headers->get('X-Inertia')) {
            return $this->redirectToRoute($name, $params);
        }
        // Sinon renvoie une redirection sans "Location" qui sera interprété apiFetch côté client
        return new Response('', 303, [
            'X-Inertia-Location' => $this->generateUrl($name, $params),
        ]);
    }

    public function getRepository(): EntityRepository
    {
        /* @var EntityRepository */
        return $this->em->getRepository($this->entityClass);
    }

    public function crudIndex(?QueryBuilder $builder): Response
    {
        $pagination = $this->paginator->paginate($builder->getQuery());

        return $this->renderComponent(sprintf('%s/index', $this->componentDirectory), [
            'pagination' => $pagination,
        ], [
            'item' => $this->itemDataClass,
        ]);
    }

    public function crudEdit(object $entity): Response
    {
        assert($entity instanceof $this->entityClass);

        return $this->renderComponent(sprintf('%s/form', $this->componentDirectory), [
            'item' => $this->mapper->map($entity, $this->formDataClass),
        ]);
    }

    public function crudCreate(): Response
    {
        return $this->renderComponent(sprintf('%s/form', $this->componentDirectory), [
            'item' => new $this->formDataClass(),
        ]);
    }

    public function crudStore(object $data, object $entity): Response
    {
        assert($entity instanceof $this->entityClass);
        assert($data instanceof $this->inputDataClass);

        $this->mapper->map($data, $entity);
        $this->em->persist($entity);
        $this->em->flush();
        if ($this->events['create'] ?? null) {
            $this->dispatcher->dispatch(new $this->events['create']($data->getEntity()));
        }
        return $this->redirectToInertiaRoute(sprintf("admin_%s_edit", $this->routePrefix), ['id' => $entity->getId()]);
    }

    public function crudUpdate(object $data, object $entity, ?object $old = null): Response
    {
        assert($entity instanceof $this->entityClass);
        assert($data instanceof $this->inputDataClass);

        $old ??= clone $entity;
        $this->mapper->map($data, $entity);
        $this->em->flush();
        if ($this->events['update'] ?? null) {
            $this->dispatcher->dispatch(new $this->events['update']($entity, $old));
        }
        return $this->redirectToInertiaRoute(sprintf('admin_%s_edit', $this->routePrefix), ['id' => $entity->getId()]);
    }
}
