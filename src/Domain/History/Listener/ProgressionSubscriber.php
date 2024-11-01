<?php

namespace App\Domain\History\Listener;

use App\Domain\Course\Entity\Course;
use App\Domain\Course\Entity\Formation;
use App\Domain\History\Entity\Progress;
use App\Domain\History\Event\ProgressEvent;
use App\Domain\History\Exception\AlreadyFinishedException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProgressionSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly EventDispatcherInterface $dispatcher)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProgressEvent::class => 'onProgress',
        ];
    }

    public function onProgress(ProgressEvent $event): void
    {
        $repository = $this->em->getRepository(Progress::class);
        /** @var ?Progress $progress */
        $progress = $repository->findOneBy([
            'content' => $event->getContent(),
            'author' => $event->getUser(),
        ]);
        if (null === $progress) {
            $progress = (new Progress())
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setCreatedAt(new \DateTimeImmutable())
                ->setAuthor($event->getUser())
                ->setContent($event->getContent())
                ->setRatio($event->getProgress());
            $this->em->persist($progress);
        } else {
            if ($progress->getRatio() >= 1) {
                throw new AlreadyFinishedException();
            }
            $progress
                ->setUpdatedAt(new \DateTimeImmutable())
                ->setRatio($event->getProgress());
        }

        // On vient de finir un tutoriel, on met alors à jour la progression dans la formation
        if ($event->getContent() instanceof Course &&
            $event->getContent()->getFormation() &&
            1.0 === $event->getProgress()
        ) {
            /** @var Formation $formation */
            $formation = $event->getContent()->getFormation();
            $courses = $formation->getCourses();
            // On récupère les Ids des tutoriels dans la formation qui ne sont pas le tutoriel de l'évènement
            $courseIds = $courses
                ->map(fn (Course $c) => $c->getId())
                ->filter(fn (?int $id) => $id !== $event->getContent()->getId())
                ->getValues();
            // On compte le nbre de tutoriels finis
            $count = $repository->count([
                'content' => $courseIds,
                'author' => $event->getUser()->getId(),
                'progress' => Progress::TOTAL,
            ]) + 1;

            // On dispatch l'évènement au parent
            $progress = $count / count($courses);
            $this->dispatcher->dispatch(new ProgressEvent($formation, $event->getUser(), $progress));
        }
    }
}
