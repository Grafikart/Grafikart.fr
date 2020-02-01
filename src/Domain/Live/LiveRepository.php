<?php

namespace App\Domain\Live;

use _HumbugBox951e2b87c765\Nette\Utils\DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Live|null find($id, $lockMode = null, $lockVersion = null)
 * @method Live|null findOneBy(array $criteria, array $orderBy = null)
 * @method Live[]    findAll()
 * @method Live[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LiveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Live::class);
    }

    /**
     * @return array<Live>
     */
    public function findForYear (int $year): array
    {
        $start = new \DateTimeImmutable("01-01-{$year}");
        $end = $start->add(new \DateInterval('P1Y'));
        return $this->createQueryBuilder('l')
            // ->where('l.created_at BETWEEN :start AND :end')
            // ->setParameter('start', $start)
            // ->setParameter('end', $end)
            ->orderBy('l.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère la date du dernier live publié sur le site
     */
    public function lastCreationDate(): ?\DateTimeInterface
    {
        $date = $this->createQueryBuilder('l')
            ->select('MAX(l.created_at)')
            ->getQuery()
            ->getSingleScalarResult();
        return $date ? new \DateTime($date) : null;
    }
}
