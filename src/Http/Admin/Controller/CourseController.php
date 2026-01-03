<?php

namespace App\Http\Admin\Controller;

use App\Component\ObjectMapper\ObjectMapperInterface;
use App\Domain\Application\Event\ContentCreatedEvent;
use App\Domain\Application\Event\ContentDeletedEvent;
use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Service\TechnologySyncService;
use App\Http\Admin\Data\Course\CourseFormData;
use App\Http\Admin\Data\Course\CourseFormInput;
use App\Http\Admin\Data\Course\CourseListItemData;
use Rompetomp\InertiaBundle\Architecture\InertiaInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

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
        InertiaInterface $inertia,
        UploaderHelper $uploaderHelper,
    ): Response {
        return $inertia->render('courses/form', [
            'course' => new CourseFormData($course, $uploaderHelper),
        ]);
    }

    #[Route(path: '/{id<\d+>}', name: 'update', methods: ['POST'])]
    public function update(
        Course $course,
        #[MapRequestPayload]
        CourseFormInput $data,
        TechnologySyncService $sync,
    ) {
        $data->hydrateEntity($course, $sync);
        $this->em->flush();

        return new JsonResponse(null);
    }
}
