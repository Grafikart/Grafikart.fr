<?php

namespace App\Domain\History\Listener;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Formation;
use App\Domain\History\Entity\Progress;
use App\Domain\History\Event\ProgressEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProgressionSubscriber implements EventSubscriberInterface
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        EntityManagerInterface $em,
        EventDispatcherInterface $dispatcher
    ) {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    public static function getSubscribedEvents()
    {
        return [
            ProgressEvent::class => 'onProgress',
        ];
    }

    public function onProgress(ProgressEvent $event): void
    {
        $repository = $this->em->getRepository(Progress::class);
        $progress = $repository->findOneBy([
            'content' => $event->getContent(),
            'author' => $event->getUser(),
        ]);
        if (null === $progress) {
            $progress = (new Progress())
                ->setUpdatedAt(new \DateTime())
                ->setCreatedAt(new \DateTime())
                ->setAuthor($event->getUser())
                ->setContent($event->getContent())
                ->setPercent($event->getPercent());
            $this->em->persist($progress);
        } else {
            $progress
                ->setUpdatedAt(new \DateTime())
                ->setPercent($event->getPercent());
        }

        // On vient de finir un tutoriel, on met alors à jour la progression dans la formation
        if (
            $event->getContent() instanceof Course &&
            $event->getContent()->getFormation() &&
            100 === $event->getPercent()
        ) {
            /** @var Formation $formation */
            $formation = $event->getContent()->getFormation();
            $courses = $formation->getCourses();
            // On récupère les Ids des tutoriels dans la formation qui ne sont pas le tutoriel de l'évènement
            $courseIds = $courses
                ->map(fn (Course $c) => $c->getId())
                ->filter(fn (int $id) => $id !== $event->getContent()->getId())
                ->getValues();
            // On compte le nbre de tutoriels finis
            $count = $repository->count([
                'content' => $courseIds,
                'percent' => 100,
            ]) + 1;

            // On dispatch l'évènement au parent
            $percent = (int) round(100 * $count / count($courses));
            $this->dispatcher->dispatch(new ProgressEvent($formation, $event->getUser(), $percent));
        }
    }
}
