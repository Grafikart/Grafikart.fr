<?php

namespace App\Http\Admin\Controller;

use ApiPlatform\Core\Api\UrlGeneratorInterface;
use App\Domain\Application\Event\ContentCreatedEvent;
use App\Domain\Application\Event\ContentDeletedEvent;
use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Helper\CourseCloner;
use App\Domain\Course\Repository\CourseRepository;
use App\Http\Admin\Data\CourseCrudData;
use App\Infrastructure\Youtube\YoutubeUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
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
     * @Route("/{id<\d+>}", name="edit", methods={"POST", "GET"})
     */
    public function edit(Request $request, Course $course): Response
    {
        $data = (new CourseCrudData($course))->setEntityManager($this->em);
        $response = $this->crudEdit($data);
        if ($request->request->get('upload')) {
            return $this->redirectToRoute('admin_course_upload', ['id' => $course->getId()]);
        }

        return $response;
    }

    /**
     * @Route("/{id<\d+>}/clone", name="clone", methods={"GET","POST"})
     */
    public function clone(Course $course): Response
    {
        $course = CourseCloner::clone($course);
        $data = new CourseCrudData($course);

        return $this->crudNew($data);
    }

    /**
     * @Route("/{id<\d+>}", methods={"DELETE"})
     */
    public function delete(Course $course): Response
    {
        return $this->crudDelete($course);
    }

    /**
     * Lance l'upload (ou la mise à jour) d'une video sur youtube.
     *
     * @Route("/upload", methods={"GET"}, name="upload")
     */
    public function upload(Request $request, CourseRepository $repository, YoutubeUploader $uploader, SessionInterface $session, EntityManagerInterface $em): Response
    {
        $sessionKey = 'course_upload_id';
        $courseId = $request->get('id') ?: $session->get($sessionKey);
        $session->set($sessionKey, $courseId);
        $code = $request->get('code');
        $uploader->setRedirectUri($this->generateUrl('admin_course_upload', [], UrlGeneratorInterface::ABS_URL));
        if (null === $code) {
            return new RedirectResponse($uploader->getAuthUrl());
        }
        $course = $repository->find($courseId);
        $course->setYoutubeId($uploader->upload($course, $code));
        $em->flush();
        $this->addFlash('success', 'La vidéo a bien été mis à jour sur Youtube');

        return $this->redirectToRoute('admin_course_edit', ['id' => $courseId]);
    }
}
