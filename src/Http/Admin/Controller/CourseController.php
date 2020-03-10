<?php

namespace App\Http\Admin\Controller;

use App\Domain\Application\Event\ContentCreatedEvent;
use App\Domain\Application\Event\ContentDeletedEvent;
use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Domain\Course\Entity\Course;
use App\Http\Admin\Data\CourseCrudData;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/course", name="course_")
 * @method getRepository() App\Domain\Course\Repository\CourseRepository\CourseRepository
 */
class CourseController extends CrudController
{

    protected string $templatePath = 'course';
    protected string $menuItem = 'course';
    protected string $entity = Course::class;
    protected string $routePrefix = 'admin_course';
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
        $query = $this->getRepository()
            ->createQueryBuilder('p')
            ->addSelect('tu', 't')
            ->leftJoin('p.technologyUsages', 'tu')
            ->leftJoin('tu.technology', 't')
            ->orderBy('p.createdAt', 'DESC');
        return $this->crudIndex($query);
    }

    /**
     * @Route("/new", name="new", methods={"POST", "GET"})
     */
    public function new(): Response
    {
        $data = new CourseCrudData();
        $data->entity = new Course();
        $data->author = $this->getUser();
        return $this->crudNew($data);
    }

    /**
     * @Route("/{id}", name="edit", methods={"POST", "GET"})
     */
    public function edit(Course $course): Response
    {
        $data = CourseCrudData::makeFromCourse($course);
        return $this->crudEdit($data);
    }

    /**
     * @Route("/{id}", methods={"DELETE"})
     */
    public function delete(Course $course): Response
    {
        return $this->crudDelete($course);
    }

}
