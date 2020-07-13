<?php

namespace App\Http\Admin\Controller;

use App\Domain\Application\Event\ContentCreatedEvent;
use App\Domain\Application\Event\ContentDeletedEvent;
use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Helper\CourseCloner;
use App\Http\Admin\Data\CourseCrudData;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/course", name="course_")
 *
 * @method getRepository() App\Domain\Course\Repository\CourseRepository\CourseRepository
 */
final class CourseController extends CrudController
{
    protected string $templatePath = 'course';
    protected string $menuItem = 'course';
    protected string $entity = Course::class;
    protected string $routePrefix = 'admin_course';
    protected array $events = [
        'update' => ContentUpdatedEvent::class,
        'delete' => ContentDeletedEvent::class,
        'create' => ContentCreatedEvent::class,
    ];

    /**
     * @Route("/", name="index")
     */
    public function index(Request $request): Response
    {
        $this->paginator->allowSort('row.id', 'row.online');
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->addSelect('tu', 't')
            ->leftJoin('row.technologyUsages', 'tu')
            ->leftJoin('tu.technology', 't')
            ->orderBy('row.createdAt', 'DESC');
        if ($request->query->has('technology')) {
            $query
                ->andWhere('t.slug = :technology')
                ->setParameter('technology', $request->query->get('technology'));
        }

        return $this->crudIndex($query);
    }

    /**
     * @Route("/new", name="new", methods={"POST", "GET"})
     */
    public function new(): Response
    {
        $entity = (new Course())->setAuthor($this->getUser());
        $data = new CourseCrudData($entity);

        return $this->crudNew($data);
    }

    /**
     * @Route("/{id}", name="edit", methods={"POST", "GET"})
     */
    public function edit(Course $course): Response
    {
        $data = (new CourseCrudData($course))->setEntityManager($this->em);

        return $this->crudEdit($data);
    }

    /**
     * @Route("/{id}/clone", name="clone", methods={"GET","POST"})
     */
    public function clone(Course $course): Response
    {
        $course = CourseCloner::clone($course);
        $data = new CourseCrudData($course);

        return $this->crudNew($data);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     */
    public function delete(Course $course): Response
    {
        return $this->crudDelete($course);
    }

    /**
     * Endpoint pour récupérer le titre d'un cours depuis son Id.
     *
     * @Route("/{id}/title", name="title")
     */
    public function title(Course $course): JsonResponse
    {
        return new JsonResponse([
            'id' => $course->getId(),
            'title' => $course->getTitle(),
        ]);
    }
}
