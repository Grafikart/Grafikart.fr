<?php

namespace App\Domain\Badge;

use App\Domain\Auth\User;
use App\Domain\Badge\Entity\Badge;
use App\Domain\Badge\Entity\BadgeUnlock;
use App\Domain\Badge\Event\BadgeUnlockEvent;
use App\Domain\Badge\Repository\BadgeUnlockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;

class BadgeService
{
    private EntityManagerInterface $em;
    private EventDispatcherInterface $dispatcher;

    public function __construct(EntityManagerInterface $em, EventDispatcherInterface $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Déclenche le déblocage du badge et renvoie l'ensemble des déblocage créés.
     *
     * @return BadgeUnlock[]|null
     */
    public function unlock(User $user, string $action, int $count = 1): ?array
    {
        /** @var BadgeUnlockRepository $repository */
        $repository = $this->em->getRepository(BadgeUnlock::class);
        $isAlreadyUnlocked = $repository->hasUnlocked($user, $action, $count);
        if ($isAlreadyUnlocked) {
            return null;
        }

        $badges = $repository->findUnlockableBadges($user, $action, $count);
        if (empty($badges)) {
            return null;
        }

        $unlocks = [];
        foreach ($badges as $badge) {
            $unlock = new BadgeUnlock($user, $badge);
            $this->em->persist($unlock);
            $unlocks[] = $unlock;
        }
        $this->em->flush();

        foreach ($unlocks as $unlock) {
            $this->dispatcher->dispatch(new BadgeUnlockEvent($unlock));
        }

        return $unlocks;
    }

    /**
     * Renvoie les badges disponibles sur le site.
     *
     * @return Badge[]
     */
    public function getBadges(): array
    {
        return $this->em->getRepository(Badge::class)->findAll();
    }

    /**
     * Renvoie les badges débloqués par l'utilisateur.
     *
     * @return BadgeUnlock[]
     */
    public function getUnlocksForUser(User $user): array
    {
        return collect($this->em->getRepository(BadgeUnlock::class)->findBy([
            'owner' => $user,
        ]))->keyBy(fn (BadgeUnlock $u) => $u->getBadge()->getId())->toArray();
    }
}
