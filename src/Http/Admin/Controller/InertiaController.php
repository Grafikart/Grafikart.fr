<?php

namespace App\Http\Admin\Controller;

use App\Domain\Application\Entity\Content;
use App\Helper\Paginator\PaginatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
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

    protected function redirectToInertiaRoute(string $name, array $params = []): Response
    {
        return new Response('', 303, [
            'X-Inertia-Location' => $this->generateUrl($name, $params),
        ]);
    }

    public function getRepository(): EntityRepository
    {
        /* @var EntityRepository */
        return $this->em->getRepository($this->entity);
    }
}
