<?php

namespace App\Http\Admin\Controller;

use App\Domain\Application\Event\ContentCreatedEvent;
use App\Domain\Application\Event\ContentDeletedEvent;
use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Repository\CourseRepository;
use App\Http\Admin\Data\Course\CourseFormData;
use App\Http\Admin\Data\Course\CourseFormInput;
use App\Http\Admin\Data\Course\CourseListItemData;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Attribute\MapUploadedFile;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @method getRepository() App\Domain\Course\Repository\CourseRepository\CourseRepository
 */
#[Route(path: '/courses', name: 'course_')]
final class CourseController extends InertiaController
{
    protected string $entityClass = Course::class;
    protected string $routePrefix = 'course';
    protected string $componentDirectory = 'courses';
    protected string $itemDataClass = CourseListItemData::class;
    protected string $formDataClass = CourseFormData::class;
    protected string $inputDataClass = CourseFormInput::class;
    protected array $events = [
        'update' => ContentUpdatedEvent::class,
        'create' => ContentCreatedEvent::class,
    ];

    #[Route(path: '/', name: 'index')]
    public function index(): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->addSelect('tu', 't')
            ->leftJoin('row.technologyUsages', 'tu')
            ->leftJoin('tu.technology', 't')
            ->orderBy('row.createdAt', 'DESC');

        return $this->crudIndex($query);
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['GET'])]
    public function edit(
        Course $course,
    ): Response {
        return $this->crudEdit($course);
    }

    #[Route(path: '/new', name: 'create', methods: ['GET'])]
    public function create(
        Request $request,
        CourseRepository $courseRepository,
    ): Response {
        $data = new CourseFormData();
        if ($request->query->has('clone')) {
            $course = $courseRepository->findOrFail($request->query->getInt('clone'));
            $course->setCreatedAt(
                (new \DateTimeImmutable(
                    '@'.$course->getCreatedAt()->getTimestamp().' +1 day'
                ))->setTimezone($course->getCreatedAt()->getTimezone())
            );
            $data = $this->mapper->map($course, CourseFormData::class);
        }

        return $this->renderComponent('courses/form', [
            'item' => $data,
        ]);
    }

    #[Route(path: '/{id<\d+>}', name: 'update', methods: ['POST'])]
    public function update(
        Course $course,
        #[MapRequestPayload]
        CourseFormInput $data,
        #[MapUploadedFile]
        ?UploadedFile $source,
    ) {
        $course->setSourceFile($source);

        return $this->crudUpdate(data: $data, entity: $course);
    }

    #[Route(path: '/new', name: 'store', methods: ['POST'])]
    public function store(
        #[MapRequestPayload]
        CourseFormInput $data,
        #[MapUploadedFile]
        ?UploadedFile $source,
    ): Response {
        $course = new Course()
            ->setUpdatedAt(new \DateTimeImmutable())
            ->setSourceFile($source);

        return $this->crudStore(
            data: $data,
            entity: $course
        );
    }

    #[Route(path: '/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(
        Course $course,
    ) {
        $course->setOnline(false);
        $course->setUpdatedAt(new \DateTimeImmutable());
        $this->em->flush();

        return $this->redirectToInertiaRoute('admin_course_index');
    }
}
