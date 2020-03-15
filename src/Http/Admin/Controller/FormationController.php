<?php

namespace App\Http\Admin\Controller;

use App\Domain\Application\Event\ContentCreatedEvent;
use App\Domain\Application\Event\ContentDeletedEvent;
use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Domain\Course\Entity\Formation;
use App\Http\Admin\Data\FormationCrudData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/formation", name="formation_")
 * @method getRepository() App\Domain\Formation\$formation\$formation\Forma
 */
final class FormationController extends CrudController
{

    protected string $templatePath = 'formation';
    protected string $menuItem = 'formation';
    protected string $entity = Formation::class;
    protected string $routePrefix = 'admin_formation';
    protected array $events = [
        'update' => ContentUpdatedEvent::class,
        'delete' => ContentDeletedEvent::class,
        'create' => ContentCreatedEvent::class
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
        $entity = (new Formation())->setAuthor($this->getUser());
        $data = new FormationCrudData($entity);
        return $this->crudNew($data);
    }

    /**
     * @Route("/{id}", name="edit", methods={"POST", "GET"})
     */
    public function edit(Formation $formation): Response
    {
        $data = (new FormationCrudData($formation))->setEntityManager($this->em);
        return $this->crudEdit($data);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     */
    public function delete(Formation $formation): Response
    {
        return $this->crudDelete($formation);
    }

}
