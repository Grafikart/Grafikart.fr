<?php

namespace App\Http\Admin\Controller;

use App\Component\ObjectMapper\ObjectMapperInterface;
use App\Domain\Application\Event\ContentCreatedEvent;
use App\Domain\Application\Event\ContentDeletedEvent;
use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Domain\Course\Entity\Course;
use App\Http\Admin\Data\Course\CourseData;
use App\Http\Admin\Data\Course\CourseFormInput;
use App\Http\Admin\Data\Course\CourseListItemData;
use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @method getRepository() App\Domain\Course\Repository\CourseRepository\CourseRepository
 */
#[Route(path: '/courses', name: 'course_')]
final class CourseController extends InertiaController
{
    protected string $entity = Course::class;
    protected array $events = [
        'update' => ContentUpdatedEvent::class,
        'delete' => ContentDeletedEvent::class,
        'create' => ContentCreatedEvent::class,
    ];

    #[Route(path: '/', name: 'index')]
    public function index(SerializerInterface $normalizer): Response
    {
        $query = $this->getRepository()
            ->createQueryBuilder('row')
            ->addSelect('tu', 't')
            ->leftJoin('row.technologyUsages', 'tu')
            ->leftJoin('tu.technology', 't')
            ->orderBy('row.createdAt', 'DESC');
        $pagination = $this->paginator->paginate($query->getQuery());

        return $this->renderComponent('courses/index', [
            'pagination' => $pagination,
        ], [
            'item' => CourseListItemData::class,
        ]);
    }

    #[Route(path: '/{id<\d+>}', name: 'edit', methods: ['GET'])]
    public function edit(
        Course $course,
        ObjectMapperInterface $objectMapper,
        InertiaInterface $inertia,
    ): Response {
        return $inertia->render('courses/form', [
            'course' => new CourseData($course),
        ]);
    }

    #[Route(path: '/{id<\d+>}', name: 'update', methods: ['POST'])]
    public function update(
        Course $course,
        #[MapRequestPayload]
        CourseFormInput $data,
        ObjectMapperInterface $mapper,
    ) {
        $course = $mapper->map($data, $course);
        dd($data);
        $this->em->flush();

        return new JsonResponse(null);
    }
}
