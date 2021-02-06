<?php

namespace App\Http\Admin\Controller;

use App\Domain\Forum\Entity\Message;
use App\Domain\Forum\Entity\Topic;
use App\Domain\Forum\Event\MessageCreatedEvent;
use App\Infrastructure\Spam\SpammableInterface;
use App\Infrastructure\Spam\SpamService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SpamController extends BaseController
{
    public const TYPES = [
        'topic' => Topic::class,
        'message' => Message::class,
    ];

    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @Route("/spam", name="spam_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('admin/spam/index.html.twig', [
            'topics' => $this->em->getRepository(Topic::class)->findBy(['spam' => true]),
            'messages' => $this->em->getRepository(Message::class)->findBy(['spam' => true]),
        ]);
    }

    /**
     * @Route("/unspam/{type}/{id<\d+>}", name="spam_undo", methods={"DELETE"})
     */
    public function unspam(string $type, int $id): JsonResponse
    {
        if (!array_key_exists($type, self::TYPES)) {
            throw new NotFoundHttpException('Type inconnu');
        }

        $entity = $this->em->getRepository(self::TYPES[$type])->find($id);
        if (null === $entity || !($entity instanceof SpammableInterface)) {
            throw new NotFoundHttpException('Aucun enregistrement ne correspond à cet ID');
        }

        $entity->setSpam(false);
        $this->em->flush();

        // On émet à nouveau l'évènement pour notifier les membre du topic
        if ($entity instanceof Message) {
            $this->dispatcher->dispatch(new MessageCreatedEvent($entity));
        }

        return $this->json([]);
    }

    /**
     * Lance la détection des contenu "spams".
     *
     * @Route("/spam/detect", name="spam_detect", methods={"POST"})
     */
    public function detect(SpamService $spamService): RedirectResponse
    {
        $topicsCount = $this->em->getRepository(Topic::class)->flagAsSpam($spamService->words());
        $messagesCount = $this->em->getRepository(Message::class)->flagAsSpam($spamService->words());
        $count = $topicsCount + $messagesCount;
        $this->addFlash('success', "{$count} spams détectés");
        if ($count > 0) {
            return $this->redirectToRoute('admin_spam_index');
        }

        return $this->redirectToRoute('admin_home');
    }
}
