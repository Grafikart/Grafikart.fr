<?php

namespace App\Http\Admin\Controller;

use App\Domain\Application\Event\ContentCreatedEvent;
use App\Domain\Application\Event\ContentDeletedEvent;
use App\Domain\Application\Event\ContentUpdatedEvent;
use App\Domain\Course\Entity\Course;
use App\Domain\Course\Helper\CourseCloner;
use App\Http\Admin\Data\CourseCrudData;
use App\Infrastructure\Youtube\YoutubeScopes;
use App\Infrastructure\Youtube\YoutubeUploaderService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/course", name="course_")
 *
 * @method getRepository() App\Domain\Course\Repository\CourseRepository\CourseRepository
 */
final class CourseController extends CrudController
{
    private const UPLOAD_SESSION_KEY = 'course_upload_id';
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
    public function edit(Request $request, Course $course, SessionInterface $session): Response
    {
        $data = (new CourseCrudData($course))->setEntityManager($this->em);
        $response = $this->crudEdit($data);
        if ($request->request->get('upload')) {
            $session->set(self::UPLOAD_SESSION_KEY, $course->getId());

            return $this->redirectToRoute('admin_course_upload');
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
    public function upload(
        Request $request,
        SessionInterface $session,
        \Google_Client $googleClient
    ): Response {
        // Si on n'a pas d'id dans la session, on redirige
        $courseId = $session->get(self::UPLOAD_SESSION_KEY);
        if (null === $courseId) {
            $this->addFlash('danger', "Impossible d'uploader la vidéo, id manquante dans la session");

            return $this->redirectToRoute('admin_course_index');
        }

        // On génère récupère le code d'auth
        $redirectUri = $this->generateUrl('admin_course_upload', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $code = $request->get('code');
        $googleClient->setRedirectUri($redirectUri);
        if (null === $code) {
            return new RedirectResponse($googleClient->createAuthUrl(YoutubeScopes::UPLOAD));
        }

        // Si on a un code d'auth, on envoie la tache à la file d'attente
        $googleClient->fetchAccessTokenWithAuthCode($request->get('code'));
        $this->dispatchMethod(
            YoutubeUploaderService::class,
            'upload',
            [(int) $courseId, $googleClient->getAccessToken()]
        );
        $this->addFlash('success', "La vidéo est en cours d'envoi sur Youtube");
        $session->remove(self::UPLOAD_SESSION_KEY);

        return $this->redirectToRoute('admin_course_edit', ['id' => $courseId]);
    }
}
