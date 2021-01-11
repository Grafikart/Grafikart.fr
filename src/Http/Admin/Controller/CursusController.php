<?php

namespace App\Http\Admin\Controller;

use App\Domain\Application\Event\ContentCreatedEvent;
use App\Domain\Application\Event\ContentDeletedEvent;
use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Domain\Course\Entity\Cursus;
use App\Domain\Course\Helper\CursusCloner;
use App\Http\Admin\Data\CursusCrudData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/cursus", name="cursus_")
 *
 * @method getRepository() App\Domain\
 */
final class CursusController extends CrudController
{
    protected string $templatePath = 'cursus';
    protected string $menuItem = 'cursus';
    protected string $entity = Cursus::class;
    protected string $routePrefix = 'admin_cursus';
    protected array $events = [
        'update' => ContentUpdatedEvent::class,
        'delete' => ContentDeletedEvent::class,
        'create' => ContentCreatedEvent::class,
    ];

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        $this->paginator->allowSort('row.id');
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->leftJoin('row.technologyUsages', 'tu')
            ->leftJoin('tu.technology', 't')
            ->addSelect('t', 'tu')
            ->orderby('row.createdAt', 'DESC')
        ;

        return $this->crudIndex($query);
    }

    /**
     * @Route("/new", name="new", methods={"POST", "GET"})
     */
    public function new(): Response
    {
        $entity = (new Cursus())->setAuthor($this->getUser());
        $data = new CursusCrudData($entity);

        return $this->crudNew($data);
    }

    /**
     * @Route("/{id<\d+>}", name="edit", methods={"POST", "GET"})
     */
    public function edit(Cursus $cursus): Response
    {
        $data = (new CursusCrudData($cursus))->setEntityManager($this->em);

        return $this->crudEdit($data);
    }

    /**
     * @Route("/{id<\d+>}/clone", name="clone", methods={"GET","POST"})
     */
    public function clone(Cursus $cursus): Response
    {
        $cursus = CursusCloner::clone($cursus);
        $data = new CursusCrudData($cursus);

        return $this->crudNew($data);
    }

    /**
     * @Route("/{id<\d+>}", methods={"DELETE"})
     */
    public function delete(Cursus $cursus): Response
    {
        return $this->crudDelete($cursus);
    }
}
