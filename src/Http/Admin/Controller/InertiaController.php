<?php

namespace App\Http\Admin\Controller;

use App\Domain\Application\Entity\Content;
use App\Helper\Paginator\PaginatorInterface;
use App\Http\Admin\Data\CrudDataInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Psr\EventDispatcher\EventDispatcherInterface;
use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

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
    protected string $entity = Content::class;
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
    ) {
    }

    protected function renderComponent(string $view, array $parameters = [], array $context = []): Response
    {
        return $this->inertia->render($view, $this->normalizer->normalize($parameters, 'json', $context));
    }

    public function getRepository(): EntityRepository
    {
        /* @var EntityRepository */
        return $this->em->getRepository($this->entity);
    }

}
