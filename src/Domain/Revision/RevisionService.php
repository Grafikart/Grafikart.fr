<?php

namespace App\Domain\Revision;

use App\Domain\Application\Entity\Content;
use App\Domain\Auth\User;
use App\Domain\Revision\Event\RevisionSubmittedEvent;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class RevisionService
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly RevisionRepository $repository,
        private readonly EntityManagerInterface $em
    ) {
    }

    /**
     * Propose une modification au contenu.
     */
    public function submitRevision(Revision $revision): void
    {
        $revision->setCreatedAt(new \DateTime());
        $isNew = null === $revision->getId();
        if ($isNew) {
            $this->em->persist($revision);
        }
        $this->em->flush();
        if ($isNew) {
            $this->eventDispatcher->dispatch(new RevisionSubmittedEvent($revision));
        }
    }

    /**
     * Renvoie la révision courante pour le contenu/utilisateur ou génère une nouvelle révision.
     */
    public function revisionFor(User $user, Content $content): Revision
    {
        $revision = $this->repository->findFor($user, $content);
        if (null !== $revision) {
            return $revision;
        }

        return (new Revision())
            ->setContent($content->getContent() ?: '')
            ->setTarget($content)
            ->setAuthor($user);
    }
}
